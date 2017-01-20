<?php /* captain.php */
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
		<form class="winner-form" action="c_process.php" method="post" name="email_form" id="email_form" autocomplete="off">
			<div class="form-row">
				Please enter the appropriate email to recieve this weeks winners list.
			</div>
			<div class="form-row">
				<label class="form-label" for="email">Email:</label>
				<input class="form-input" type="email" name="email" id="email" form="email_form" placeholder="email" title="**Required** Please enter your Email!" required="required" />
			</div>
			<div class="form-row">
				<input type="hidden" name="prize_id" id="prize_id" value="<?php echo $prize_id; ?>" />
				<input class="form-button" type="submit" name="submit" form="email_form" value="Get List!" />
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
	<title>Spin-O-Rama</title>
	<link rel="stylesheet" type="text/css" media="screen" href="styles/spin-o-rama.css" />
</head>
<body>
	<div id="container">
		<section class="winner-form">
			The winners list is on its way!
		</section>
	</div>
</body>
</html>
<?php
	}
?>