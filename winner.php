<?php /* winner.php */
	$prize_id = $_GET['p'];
	$conclusion = $_GET['c'];

	if($conclusion != 'true') {
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Spin-O-Rama</title>
	<link rel="stylesheet" type="text/css" media="screen" href="styles/spin-o-rama.css" />
</head>
<body>
	<div id="container">
		<form class="winner-form" action="process.php" method="post" name="winner_form" id="winner_form" autocomplete="off">
			<div class="form-row">
				** To claim your prize, we need your full name and email address. All certificates will be <strong>EMAILED</strong> to you. All certificates are subject to 90 days expiration from the date of issuance (today) and can only be redeemed by the person whose name is provided on this form. **
			</div>
			<div class="form-row">
				<label class="form-label" for="fullname">Full Name:</label>
				<input class="form-input" type="text" name="fullname" id="fullname" form="winner_form" placeholder="full name..." title="**Required** Please enter your Full Name!" required="required" />
			</div>
			<div class="form-row">
				<label class="form-label" for="email">Email Address:</label>
				<input class="form-input" type="email" name="email" id="email" form="winner_form" placeholder="email..." title="**Required** Please enter your Email!" required="required" />
			</div>
			<div class="form-row">
				<label class="form-label" for="email2">Confirm Email:</label>
				<input class="form-input" type="email" name="email2" id="email2" form="winner_form" placeholder="email conf..." title="**Required** Please enter your Email again to confirm!" required="required" />
			</div>
			<div class="form-row">
				<input type="hidden" name="prize_id" id="prize_id" value="<?php echo $prize_id; ?>" />
				<input class="form-button" type="submit" name="submit" form="winner_form" value="Claim Prize!" />
			</div>
		</form>
	</div>
</body>
</html>
<?php
	} else {
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $sb['sb_name']; ?> Spin-O-Rama</title>
	<link rel="stylesheet" type="text/css" media="screen" href="styles/spin-o-rama.css" />
</head>
<body>
	<div id="container">
		<section class="winner-form">
			Thank you for completing the claim form.<br>You should be recieving an email shortly.<br>Remember to play again next Tuesday between 9am and 10am!
		</section>
	</div>
</body>
</html>
<?php
	}
?>