<?php /* process.php */
	$pdo_host = 'localhost'; // Create pdo_host variable	$pdo_name = 'database'; // Create pdo_name variable	$pdo_declare = 'mysql:host=' . $pdo_host . ';dbname=' . $pdo_name . ''; // Create pdo_declare variable	$pdo_user = 'username'; // Create pdo_user variable	$pdo_pass = 'password'; // Create pdo_pass variable
	$conn = new PDO($pdo_declare, $pdo_user, $pdo_pass); // Set up PDO connection
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set up PDO error handling

	// Create find_ip function
	function find_ip() {
		$ip_var = NULL; // Create ip_var variable
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip_var = $_SERVER['HTTP_CLIENT_IP']; // Adjust ip_var variable
		} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_var = $_SERVER['HTTP_X_FORWARDED_FOR']; // Adjust ip_var variable
		} elseif(!empty($_SERVER['REMOTE_ADDR'])) {
			$ip_var = $_SERVER['REMOTE_ADDR']; // Adjust ip_var variable
		} else {
			$ip_var = 'HIDDEN'; // Adjust ip_var variable
		}

		return $ip_var;
	}

	// Create check_user function
	function check_user($c, $ip_var) {
		$test = NULL;
		$sql = $c->prepare('SELECT user_id, user_date, user_email FROM users WHERE user_ip = ? ORDER BY user_id DESC');
		$sql->bindParam(1, $ip_var, PDO::PARAM_STR); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$test = $sql->fetch();
		if($test['user_id'] != NULL) {
			if($test['user_email'] == NULL) {
				return TRUE;
			} else {
				return FALSE; // Change to FALSE to enable 1 per week
			}
		} else {
			return FALSE; // Change to FALSE to enable 1 per week
		}
	}

	$ip_var = find_ip();

	if($ip_var != NULL) {
		$day = "2";
		$hour = "06";
		if($day != "2") {
			$verify = FALSE;
		} elseif($hour != "06") {
			$verify = FALSE;
		} else {
			$verify = check_user($conn, $ip_var);
		}

		switch($verify) {
			case FALSE:
				echo "You already claimed a prize today. Hacking attempts are totally not cool!";
				exit;
				break;
			case TRUE:
				// Allow continue
				break;
		}
	}

	// Create validate_email function
	function validate_email($email) {
		return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
	}

	// Create validate_input function
	function validate_input($string, $type, $length) {
		$type = 'is_'.$type; // Adjust type variable

		if(!$type($string)) {
			return FALSE; // Kill process
		} elseif(empty($string)) {
			return FALSE; // Kill process
		} elseif(strlen($string) > $length) {
			return FALSE; // Kill process
		} else {
			return TRUE; // Continue process
		}
	}

	// Create send_email function
	function send_email($conn, $player_name, $player_email, $prize_id, $var_ip) {
		$sql = $conn->prepare('SELECT p.prize_name, p.prize_description, p.prize_quantity, s.sb_name, s.sb_code, s.sb_codecount, s.sb_id FROM prizes AS p INNER JOIN prize_location AS pl ON pl.prize_id = p.prize_id INNER JOIN switchboard AS s ON s.sb_id = pl.switchboard_id WHERE p.prize_id = ? AND p.prize_active = 1 AND p.prize_delete = 0 AND s.sb_open = 1');
		$sql->bindParam(1, $prize_id, PDO::PARAM_INT); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$prize_info = $sql->fetch();
		$prize_name = $prize_info['prize_name'];
		$prize_description = $prize_info['prize_description'];
		$prize_location = $prize_info['sb_name'];
		$prize_loc_id = $prize_info['sb_id'];
		$prize_quantity = $prize_info['prize_quantity'];
		$prize_code = $prize_info['sb_code'] . str_pad((int)$prize_info['sb_codecount'], 6, "0", STR_PAD_LEFT);

		$sql2 = $conn->prepare('SELECT user_id FROM users WHERE user_ip = ? ORDER BY user_id DESC LIMIT 1');
		$sql2->bindParam(1, $var_ip, PDO::PARAM_STR); // Bind parameter
		$sql2->execute(); // Execute prepared statement
		$user_id = $sql2->fetch();
		$user_id = $user_id['user_id'];

		$sql3 = $conn->prepare('UPDATE users SET user_email = ? WHERE user_ip = ? AND user_id = ?');
		$sql3->bindParam(1, $player_email, PDO::PARAM_STR); // Bind parameter
		$sql3->bindParam(2, $var_ip, PDO::PARAM_STR); // Bind parameter
		$sql3->bindParam(3, $user_id, PDO::PARAM_INT); // Bind parameter
		$sql3->execute(); // Execute prepared statement

		$sql4 = $conn->prepare('INSERT INTO winners (user_id, prize_id, date, winner_code, winner_fullname) VALUES (?, ?, ?, ?, ?)');
		$sql4->bindParam(1, $user_id, PDO::PARAM_INT); // Bind parameter
		$sql4->bindParam(2, $prize_id, PDO::PARAM_INT); // Bind parameter
		$sql4->bindParam(3, date('U'), PDO::PARAM_INT); // Bind parameter
		$sql4->bindParam(4, $prize_code, PDO::PARAM_STR); // Bind parameter
		$sql4->bindParam(5, $player_name, PDO::PARAM_STR); // Bind parameter
		$sql4->execute(); // Execute prepared statement

		$codecount = ($prize_info['sb_codecount'] + 1);
		$sql5 = $conn->prepare('UPDATE switchboard SET sb_codecount = ? WHERE sb_id = ?');
		$sql5->bindParam(1, $codecount, PDO::PARAM_INT); // Bind parameter
		$sql5->bindParam(2, $prize_loc_id, PDO::PARAM_INT); // Bind parameter
		$sql5->execute(); // Execute prepared statement

		$prizequantity = ($prize_quantity - 1);
		$sql6 = $conn->prepare('UPDATE prizes SET prize_quantity = ? WHERE prize_id = ?');
		$sql6->bindParam(1, $prizequantity, PDO::PARAM_INT); // Bind parameter
		$sql6->bindParam(2, $prize_id, PDO::PARAM_INT); // Bind parameter
		$sql6->execute(); // Execute prepared statement

		$create_message = "
			<!DOCTYPE html>
			<html>
			<head>
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
				<title>Spin-O-Rama</title>
			</head>
			<body>
				<table cellspacing='0' cellpadding='0' style='width: 600px; border: 2px dashed #000000; border-collapse: collapse; height: 300px;'>
					<tr>
						<td style='height: 60px; border-bottom: 1px solid #000000;' colspan='3'>
							<table cellspacing='0' cellpadding='0' style='width: 600px; border-collapse: collapse; height: 60px;'>
								<tr>
									<td style='width: 150px; padding-left: 10px; height: 60px;'></td>
									<td style='font-size: 2em; text-align: center;'>Gift Certificate</td>
									<td style='width: 150px; font-size: 0.6em; text-align: right; vertical-align: top; padding-right: 10px; padding-top: 10px;'>Expires:<br />" . date('m-d-Y', strtotime('+90 days')) . "</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><img src='http://www.macmannis.com/slot/images/" . $prize_loc_id . ".jpg' alt='LOGO' width='150' height='150' style='padding-top: 25px; padding-bottom: 25px;' /></td>
						<td style='font-size: 2.5em; text-align: center; height: 200px;'>
							<strong>" . $prize_name . "</strong><br />
							@ <em>" . $prize_location . "</em>
						</td>
						<td><img src='http://www.macmannis.com/slot/images/" . $prize_loc_id . ".jpg' alt='LOGO' width='150' height='150' style='padding-top: 25px; padding-bottom: 25px;' /></td>
					</tr>
					<tr>
						<td style='border-top: 1px solid #000000; height: 40px;' colspan='3'>
							<table cellspacing='0' cellpadding='0' style='width: 600px; border-collapse: collapse;'>
								<tr>
									<td style='width: 200px; padding-left: 5px; height: 40px;'>Redeem By: " . $player_name . "</td>
									<td></td>
									<td style='width: 200px; padding-right: 5px;'>Code: " . $prize_code . "</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</body>
			</html>";

		$mail_to = $player_name . ' <' . $player_email . '>'; // Create mail_to variable
		$mail_subject = 'Prize From The ' . $prize_location . ' Slot Machine'; // Create mail_subject variable
		$mail_message = wordwrap($create_message . '<br /><br />Please print out this email and present the certificate prior to ordering!<br />Name shown on certificate is sole redeemer of certificate.<br />Expires 90 days from date of issuance.<br />Manager reserves the right to void certificate.<br />Certificate holds no cash equivalent value.', 70); // Create mail_message variable
		$mail_headers = 'From: no_reply@macmannis.com' . "\r\n" .
			'Reply-To: no_reply@macmannis.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
			'X-Mailer: PHP/' . phpversion(); // Create mail_headers variable
		mail($mail_to, $mail_subject, $mail_message, $mail_headers); // Mail object
	}

	if(validate_input($_POST['fullname'], 'string', 50) == TRUE && validate_input($_POST['email'], 'string', 60) == TRUE && validate_input($_POST['email2'], 'string', 60) == TRUE && validate_email($_POST['email']) == TRUE && validate_email($_POST['email2']) == TRUE) {
		
		send_email($conn, $_POST['fullname'], $_POST['email'], $_POST['prize_id'], $ip_var);
		echo "<meta http-equiv='refresh' content='0;url=thanks.php' />";
		exit;
	} else {
		echo "Your input failed to validate, please go back and try again!";
		exit;
	}
?>