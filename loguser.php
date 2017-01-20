<?php /* loguser.php */
	$ip_var = NULL;

	$pdo_host = 'localhost'; // Create pdo_host variable	$pdo_name = 'database'; // Create pdo_name variable	$pdo_declare = 'mysql:host=' . $pdo_host . ';dbname=' . $pdo_name . ''; // Create pdo_declare variable	$pdo_user = 'username'; // Create pdo_user variable	$pdo_pass = 'password'; // Create pdo_pass variable
	$conn = new PDO($pdo_declare, $pdo_user, $pdo_pass); // Set up PDO connection
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set up PDO error handling

	// Create find_ip function
	function find_ip() {
		$ip_var = ''; // Create ip_var variable
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

	// Create insert_user function
	function insert_user($c, $ip_var) {
		$sql = $c->prepare('INSERT INTO users (user_ip, user_date) VALUES (?, ?)');
		$sql->bindParam(1, $ip_var, PDO::PARAM_STR); // Bind parameter
		$sql->bindParam(2, date('U'), PDO::PARAM_STR); // Bind parameter
		$sql->execute(); // Execute prepared statement
	}

	// Create check_user function
	function check_user($c, $ip_var) {
		$test = NULL;
		$sql = $c->prepare('SELECT user_id, user_date FROM users WHERE user_ip = ? ORDER BY user_id DESC');
		$sql->bindParam(1, $ip_var, PDO::PARAM_STR); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$test = $sql->fetch();
		if($test['user_id'] != NULL) {
			$last = $test['user_date'];
			$today = date("U");
			$diff = $today - $last;
			if($diff >= 601200) {
				return TRUE;
			} else {
				return FALSE; // Change to FALSE to enable 1 per week
			}
		} else {
			return TRUE;
		}
	}

	$ip_var = find_ip();

	if($ip_var != NULL)
	{
		$day = "2";
		$hour = "06";
		if($day != "2") {
			$verify = FALSE;
		} elseif($hour != "06") {
			$verify = FALSE;
		} else {
			$verify = check_user($conn, $ip_var);
		}

		switch($verify)
		{
			case FALSE:
				echo 'FAIL';
				break;
			case TRUE:
				insert_user($conn, $ip_var);
				echo 'SUCCESS';
				break;
		}
	}
?>