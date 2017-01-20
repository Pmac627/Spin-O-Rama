<?php /* c_process.php */
	$pdo_host = 'localhost'; // Create pdo_host variable	$pdo_name = 'database'; // Create pdo_name variable	$pdo_declare = 'mysql:host=' . $pdo_host . ';dbname=' . $pdo_name . ''; // Create pdo_declare variable	$pdo_user = 'username'; // Create pdo_user variable	$pdo_pass = 'password'; // Create pdo_pass variable
	$conn = new PDO($pdo_declare, $pdo_user, $pdo_pass); // Set up PDO connection
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set up PDO error handling

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

	function match_email($c, $email) {
		$sql = $c->prepare('SELECT captain_id FROM captains WHERE email = ?');
		$sql->bindParam(1, $email, PDO::PARAM_STR); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$captain = $sql->fetch();
		$captain = $captain['captain_id'];
		if($captain != NULL) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// Create send_email function
	function send_email($conn, $email) {
		$today = date("U");
		$timeframe = ($today - 601200);
		$sql = $conn->prepare('SELECT u.user_email, w.winner_fullname, w.winner_code, w.date, p.prize_name FROM users AS u INNER JOIN winners AS w ON w.user_id = u.user_id INNER JOIN prizes AS p ON p.prize_id = w.prize_id WHERE w.date > ? ORDER BY w.winner_code, w.winner_fullname');
		$sql->bindParam(1, $timeframe, PDO::PARAM_INT); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$winners_list = $sql->fetchAll();
		$count = 0;

		$create_message = "
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
			<title>Spin-O-Rama</title>
		</head>
		<body>
			<table cellspacing='0' cellpadding='0' style='width: 1000px; background-color: #FFFFFF; border: 3px double #000000; border-collapse: collapse; background-image: none; height: auto;'>
				<tr>
					<th style='width: 220px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Full Name</th>
					<th style='width: 110px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Code</th>
					<th style='width: 220px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Prize</th>
					<th style='width: 250px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Email</th>
					<th style='width: 100px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Date</th>
					<th style='width: 100px; border: 1px solid #000000; border-collapse: collapse; padding: 2px;'>Redeem Date</th>
				</tr>";

			foreach($winners_list as $w) {
				if($count == 0) {
					$create_message .= "
				<tr>";
					$count++;
				} else {
					$create_message .= "
				<tr style='background-color: #EEEEEE;'>";
					$count--;
				}
					$create_message .= "
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px;'> " . $w['winner_fullname'] . "</td>
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px; text-align: center;'>" . $w['winner_code'] . "</td>
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px; text-align: center;'>" . $w['prize_name'] . "</td>
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px; text-align: center;'>" . $w['user_email'] . "</td>
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px; text-align: center;'>" . date("m-d-Y", $w['date']) . "</td>
					<td style='border: 1px solid #000000; border-collapse: collapse; padding: 2px;'></td>
				</tr>";
			}
			$create_message .= "
			</table>
		</body>
		</html>";

		$mail_to = $email; // Create mail_to variable
		$mail_subject = 'Prize List From Spin-O-Rama!'; // Create mail_subject variable
		$mail_message = wordwrap('Here is this weeks prize list for SPIN-O-RAMA!<br /><br />' . $create_message . '<br /><br />Print in <strong>LANDSCAPE</strong> view!', 70); // Create mail_message variable
		$mail_headers = 'From: no_reply@macmannis.com' . "\r\n" .
			'Reply-To: no_reply@macmannis.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
			'X-Mailer: PHP/' . phpversion(); // Create mail_headers variable
		mail($mail_to, $mail_subject, $mail_message, $mail_headers); // Mail object
	}

	if(validate_input($_POST['email'], 'string', 60) == TRUE && validate_email($_POST['email']) == TRUE) {
		if(match_email($conn, $_POST['email']) == TRUE) {
			send_email($conn, $_POST['email']);
			header("Location: captain.php?c=true");
			exit;
		} else {
			echo "Your email is not authorized to use this script!";
			exit;
		}
	} else {
		echo "Your input failed to validate, please go back and try again!";
		exit;
	}
?>