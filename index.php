<?php /* index.php */
	$ip_var = NULL;
	$loc = 1;

	$pdo_host = 'localhost'; // Create pdo_host variable
	$pdo_name = 'database'; // Create pdo_name variable
	$pdo_declare = 'mysql:host=' . $pdo_host . ';dbname=' . $pdo_name . ''; // Create pdo_declare variable
	$pdo_user = 'username'; // Create pdo_user variable
	$pdo_pass = 'password'; // Create pdo_pass variable
	$conn = new PDO($pdo_declare, $pdo_user, $pdo_pass); // Set up PDO connection
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set up PDO error handling

	// Create sb function
	function sb($c, $location) {
		$sql = $c->prepare('SELECT sb_name, sb_winall, sb_start, sb_end FROM switchboard WHERE sb_id = ? AND sb_open = 1');
		$sql->bindParam(1, $location, PDO::PARAM_STR); // Bind parameter
		$sql->execute(); // Execute prepared statement
		$sb = $sql->fetch();
		return $sb;
	}

	$sb = sb($conn, $loc);
	$sb_winall = $sb['sb_winall'];

	// Create prize_info function
	function prize_info($c, $winall, $location) {
		if($winall == 1) {
			$sql = $c->prepare('SELECT p.prize_id, p.prize_quantity, p.prize_description, p.prize_frequency, c.combination, s.sb_id, s.sb_code FROM prizes AS p INNER JOIN prize_combination AS pc ON p.prize_id = pc.prize_id INNER JOIN combinations AS c ON c.combination_id = pc.combination_id INNER JOIN prize_location AS pl ON pl.prize_id = p.prize_id INNER JOIN switchboard AS s ON s.sb_id = pl.switchboard_id WHERE p.prize_active = 1 AND p.prize_delete = 0 AND c.combination = ?');
			$sql->bindValue(1, 'any', PDO::PARAM_STR); // Bind parameter
			$sql->execute(); // Execute prepared statement
			$prize_info = $sql->fetchAll();
			return $prize_info;
		} else {
			$sql = $c->prepare('SELECT p.prize_id, p.prize_quantity, p.prize_description, p.prize_frequency, c.combination, s.sb_id, s.sb_code FROM prizes AS p INNER JOIN prize_combination AS pc ON p.prize_id = pc.prize_id INNER JOIN combinations AS c ON c.combination_id = pc.combination_id INNER JOIN prize_location AS pl ON pl.prize_id = p.prize_id INNER JOIN switchboard AS s ON s.sb_id = pl.switchboard_id WHERE p.prize_active = 1 AND p.prize_delete = 0');
			$sql->execute(); // Execute prepared statement
			$prize_info = $sql->fetchAll();
			return $prize_info;
		}
	}

	$prize_array = prize_info($conn, $sb_winall, $loc);
	$total_prizes = count($prize_array);
	if($sb_winall == 1) {
		$winning_prize = mt_rand(1, $total_prizes);
	} else {
		$total = (12 * $total_prizes);
		$winning = mt_rand(1, $total);
		if($winning >= 1 && $winning <= $total_prizes) {
			$winning_prize = mt_rand(1, $winning);
		}
	}
	$count = 0;
	$final_prize = array();
	foreach($prize_array AS $prizes) {
		$count++;
		if($count == $winning_prize) {
			$final_prize['prize_id'] = $prizes['prize_id'];
			$final_prize['prize_description'] = $prizes['prize_description'];
			if($prizes['combination'] == 'any') {
				$random_combo = mt_rand(1, 6);
				switch($random_combo) {
					case 1:
						$final_prize['prize_combination'] = 'd-c-d-c-d';
						break;
					case 2:
						$final_prize['prize_combination'] = 'o-c-o-c-o';
						break;
					case 3:
						$final_prize['prize_combination'] = 'p-c-p-c-p';
						break;
					case 4:
						$final_prize['prize_combination'] = 'o-c-c-o-o';
						break;
					case 5:
						$final_prize['prize_combination'] = 'd-d-c-c-d';
						break;
					case 6:
						$final_prize['prize_combination'] = 'c-p-p-p-c';
						break;
					default:
						$final_prize['prize_combination'] = 'cb-bb-npc-bc-npm';
						break;
				}
			} else {
				$final_prize['prize_combination'] = $prizes['combination'];
			}
		} else {
			// Do nothing and advance
		}
	}

	$split_combo = explode("-", $final_prize['prize_combination']);

	function combo_slot_reel($array_combo) {
		$spot = NULL;
		switch($array_combo) {
			case 'npc':
				$spot = 0;
				break;
			case 'bb':
				$spot = 323;
				break;
			case 'c':
				$spot = 646;
				break;
			case 'npm':
				$spot = 969;
				break;
			case 'o':
				$spot = 1292;
				break;
			case 'bc':
				$spot = 1615;
				break;
			case 'd':
				$spot = 1938;
				break;
			case 'cb':
				$spot = 2261;
				break;
			case 'p':
				$spot = 2584;
				break;
		}

		return $spot;
	}

	switch($final_prize['prize_combination']) {
		case 'd-c-d-c-d':
			$message = $final_prize['prize_description'];
			break;
		case 'o-c-o-c-o':
			$message = $final_prize['prize_description'];
			break;
		case 'p-c-p-c-p':
			$message = $final_prize['prize_description'];
			break;
		case 'o-c-c-o-o':
			$message = $final_prize['prize_description'];
			break;
		case 'd-d-c-c-d':
			$message = $final_prize['prize_description'];
			break;
		case 'c-p-p-p-c':
			$message = $final_prize['prize_description'];
			break;
		case 'cb-bb-npc-bc-npm':
			$message = $final_prize['prize_description'];
			break;
		default:
			$message = 'You didn\'t win this time. Try again next Tuesday!';
			break;
	}

	// Create count_users function
	function count_users($c) {
		$sql = $c->prepare('SELECT user_id FROM users');
		$sql->execute(); // Execute prepared statement
		$rows = $sql->rowCount();
		$rows = str_pad((int) $rows, 8, "0", STR_PAD_LEFT);
		return $rows;
	}

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

	$totals = count_users($conn);
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
				$try_again = "alert('Either you already played this week or its not Tuesday between 9:00am and 10:00am EST. Come back and try again then!');";
				$credits = "0 CREDIT";
				break;
			case TRUE:
				$try_again = "";
				$credits = "1 CREDIT";
				break;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $sb['sb_name']; ?> Spin-O-Rama</title>
	<link rel="stylesheet" type="text/css" media="screen" href="styles/spin-o-rama.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script src="js/jquery.spritely.js"></script>
	<script src="js/jquery.backgroundPosition.js"></script>
</head>
<body onload="preload('images/reel_blur.jpg', 'images/claim.png');">
	<div id="container">
		<table id="slotmachine">
			<tr id="row1">
				<td class="row1-column1" colspan="13"></td>
			</tr>
			<tr id="row2">
				<td class="row2-column1" colspan="11"></td>
				<td class="row2-column2" rowspan="2"><button id="control">SPIN</button></td>
				<td class="row2-column3"></td>
			</tr>
			<tr id="row3">
				<td class="row3-column1"></td>
				<td class="row3-column2" rowspan="2"><div id="slot1" class="slot1"><img src="images/spinner-glass.png" alt="" width="98" height="323" /></div></td>
				<td class="row3-column3"></td>
				<td class="row3-column4" rowspan="2"><div id="slot2" class="slot2"><img src="images/spinner-glass.png" alt="" width="97" height="323" /></div></td>
				<td class="row3-column5"></td>
				<td class="row3-column6" rowspan="2"><div id="slot3" class="slot3"><img src="images/spinner-glass.png" alt="" width="97" height="323" /></div></td>
				<td class="row3-column7"></td>
				<td class="row3-column8" rowspan="2"><div id="slot4" class="slot4"><img src="images/spinner-glass.png" alt="" width="97" height="323" /></div></td>
				<td class="row3-column9"></td>
				<td class="row3-column10" rowspan="2"><div id="slot5" class="slot5"><img src="images/spinner-glass.png" alt="" width="97" height="323" /></div></td>
				<td class="row3-column11 clear"></td>
				<!-- ROWSPAN (COLUMN 2, ROW 2) -->
				<td class="row3-column13"></td>
			</tr>
			<tr id="row4">
				<td class="row4-column1"></td>
				<!-- ROWSPAN (COLUMN 2, ROW 3) -->
				<td class="row4-column3"></td>
				<!-- ROWSPAN (COLUMN 4, ROW 3) -->
				<td class="row4-column5"></td>
				<!-- ROWSPAN (COLUMN 6, ROW 3) -->
				<td class="row4-column7"></td>
				<!-- ROWSPAN (COLUMN 8, ROW 3) -->
				<td class="row4-column9"></td>
				<!-- ROWSPAN (COLUMN 10, ROW 3) -->
				<td class="row4-column11"></td>
				<td class="row4-column12"></td>
				<td class="row4-column13"></td>
			</tr>
			<tr id="row5">
				<td class="row5-column1" colspan="13"></td>
			</tr>
			<tr id="row6">
				<td class="row6-column1"></td>
				<td class="row6-column2" id="totals" colspan="4"><?php echo $totals; ?></td>
				<td class="row6-column3" id="credits" colspan="5"><?php echo $credits; ?></td>
				<td class="row6-column4"></td>
				<td class="row6-column5"></td>
				<td class="row6-column6"></td>
			</tr>
			<tr id="row7">
				<td class="row7-column1" colspan="13"></td>
			</tr>
			<tr id="row8">
				<td class="row8-column1" id="result" colspan="11"></td>
				<td class="row8-column4"></td>
				<td class="row8-column5"></td>
			</tr>
			<tr id="row9">
				<td class="row9-column1" colspan="13"></td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		function preload() {
			var d = document;
			if(d.images) {
				if(!d.p) {
					d.p = new Array();
					var i, j = d.p.length, a = preload.arguments;
					for(i = 0; i < a.length; i++) {
						if(a[i].indexOf("#") != 0) {
							d.p[j] = new Image; d.p[j++].src = a[i];
						}
					}
				}
			}
		}
	</script>
	<script type="text/javascript">
		// When Spin Is Clicked...
		button_clicked = false;
		$(document).ready(function () {
			$('#control').click(function(e) {
				if(button_clicked == false)
				{
					<?php echo $try_again; ?>
					button_clicked = true;
					e.preventDefault();
					e.stopPropagation();
					log_user();
				}
			});
		});

		function log_user() {
			$.ajax({
				type: "POST",
				url: "loguser.php",
				success: function(data) {
					if(data == 'FAIL') {
						// Do Nothing
					} else {
						/**
						* Global variables
						*/
						var completed = 0,
							imgHeight = 2907,
							posArr = [
								0, //np cran
								323, //bamboo
								646, //cash
								969, //np mc
								1292, //oranges
								1615, //b&c
								1938, //dice
								2261, //cabana
								2584 //palms
							];

						var win = [];
						win[0] = 1;
						win[323] = 2;
						win[646] = 3;
						win[969] = 4;
						win[1292] = 5;
						win[1615] = 6;
						win[1938] = 7;
						win[2261] = 8;
						win[2584] = 9;

						/**
						* @class Slot
						* @constructor
						*/
						function Slot(el, max, step) {
							this.speed = 0; //speed of the slot at any point of time
							this.step = step; //speed will increase at this rate
							this.si = null; //holds setInterval object for the given slot
							this.el = el; //dom element of the slot
							this.maxSpeed = max; //max speed this slot can have
							this.pos = null; //final position of the slot

							$(el).pan({
								fps:30,
								dir:'down'
							});
							$(el).spStop();
						}

						/**
						* @method start1
						* Starts slot1
						*/
						Slot.prototype.start1 = function() {
							var _this = this;
							$(_this.el).addClass('motion1');
							$(_this.el).spStart();
							_this.si = window.setInterval(function() {
								if(_this.speed < _this.maxSpeed) {
									_this.speed += _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
							}, 100);
						};

						/**
						* @method start2
						* Starts slot2
						*/
						Slot.prototype.start2 = function() {
							var _this = this;
							$(_this.el).addClass('motion2');
							$(_this.el).spStart();
							_this.si = window.setInterval(function() {
								if(_this.speed < _this.maxSpeed) {
									_this.speed += _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
							}, 100);
						};

						/**
						* @method start3
						* Starts slot3
						*/
						Slot.prototype.start3 = function() {
							var _this = this;
							$(_this.el).addClass('motion3');
							$(_this.el).spStart();
							_this.si = window.setInterval(function() {
								if(_this.speed < _this.maxSpeed) {
									_this.speed += _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
							}, 100);
						};

						/**
						* @method start4
						* Starts slot4
						*/
						Slot.prototype.start4 = function() {
							var _this = this;
							$(_this.el).addClass('motion4');
							$(_this.el).spStart();
							_this.si = window.setInterval(function() {
								if(_this.speed < _this.maxSpeed) {
									_this.speed += _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
							}, 100);
						};

						/**
						* @method start5
						* Starts slot5
						*/
						Slot.prototype.start5 = function() {
							var _this = this;
							$(_this.el).addClass('motion5');
							$(_this.el).spStart();
							_this.si = window.setInterval(function() {
								if(_this.speed < _this.maxSpeed) {
									_this.speed += _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
							}, 100);
						};

						/**
						* @method stop1
						* Stops a slot
						*/
						Slot.prototype.stop1 = function() {
							var _this = this,
								limit = 20;
							clearInterval(_this.si);
							_this.si = window.setInterval(function() {
								if(_this.speed > limit) {
									_this.speed -= _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
								if(_this.speed <= limit) {
									_this.finalPos1(_this.el);
									$(_this.el).spSpeed(0);
									$(_this.el).spStop();
									clearInterval(_this.si);
									$(_this.el).removeClass('motion1');
									_this.speed = 0;
								}
							}, 100);
						};

						/**
						* @method stop2
						* Stops a slot
						*/
						Slot.prototype.stop2 = function() {
							var _this = this,
								limit = 20;
							clearInterval(_this.si);
							_this.si = window.setInterval(function() {
								if(_this.speed > limit) {
									_this.speed -= _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
								if(_this.speed <= limit) {
									_this.finalPos2(_this.el);
									$(_this.el).spSpeed(0);
									$(_this.el).spStop();
									clearInterval(_this.si);
									$(_this.el).removeClass('motion2');
									_this.speed = 0;
								}
							}, 100);
						};

						/**
						* @method stop3
						* Stops a slot
						*/
						Slot.prototype.stop3 = function() {
							var _this = this,
								limit = 20;
							clearInterval(_this.si);
							_this.si = window.setInterval(function() {
								if(_this.speed > limit) {
									_this.speed -= _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
								if(_this.speed <= limit) {
									_this.finalPos3(_this.el);
									$(_this.el).spSpeed(0);
									$(_this.el).spStop();
									clearInterval(_this.si);
									$(_this.el).removeClass('motion3');
									_this.speed = 0;
								}
							}, 100);
						};

						/**
						* @method stop4
						* Stops a slot
						*/
						Slot.prototype.stop4 = function() {
							var _this = this,
								limit = 20;
							clearInterval(_this.si);
							_this.si = window.setInterval(function() {
								if(_this.speed > limit) {
									_this.speed -= _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
								if(_this.speed <= limit) {
									_this.finalPos4(_this.el);
									$(_this.el).spSpeed(0);
									$(_this.el).spStop();
									clearInterval(_this.si);
									$(_this.el).removeClass('motion4');
									_this.speed = 0;
								}
							}, 100);
						};

						/**
						* @method stop5
						* Stops a slot
						*/
						Slot.prototype.stop5 = function() {
							var _this = this,
								limit = 20;
							clearInterval(_this.si);
							_this.si = window.setInterval(function() {
								if(_this.speed > limit) {
									_this.speed -= _this.step;
									$(_this.el).spSpeed(_this.speed);
								}
								if(_this.speed <= limit) {
									_this.finalPos5(_this.el);
									$(_this.el).spSpeed(0);
									$(_this.el).spStop();
									clearInterval(_this.si);
									$(_this.el).removeClass('motion5');
									_this.speed = 0;
								}
							}, 100);
						};

						/**
						* @method finalPos1
						* Finds the final position of the slot
						*/
						Slot.prototype.finalPos1 = function() {
							var el = this.el,
								el_id,
								pos,
								posMin = 2000000000,
								best,
								bgPos,
								i,
								j,
								k;

							el_id = $(el).attr('id');
							pos = document.getElementById(el_id).style.backgroundPosition;
							pos = pos.split(' ')[1];
							pos = parseInt(pos, 10);

							for(i = 0; i < posArr.length; i++) {
								for(j = 0;;j++) {
									k = posArr[i] + (imgHeight * j);
									if(k > pos) {
										if((k - pos) < posMin) {
											posMin = k - pos;
											//best = k;
											// 0	np cran
											// 323	bamboo
											// 646	cash
											// 969	np mc
											// 1292	oranges
											// 1615	b&c
											// 1938	dice
											// 2261	cabana
											// 2584	palms
											best = <?php echo combo_slot_reel($split_combo[0]); ?>; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
											//this.pos = posArr[i]; //update the final position of the slot
											this.pos = <?php echo combo_slot_reel($split_combo[0]); ?>; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
										}
										break;
									}
								}
							}

							best += imgHeight + 4;
							bgPos = "0 " + best + "px";
							$(el).animate({
								backgroundPosition:"(" + bgPos + ")"
							}, {
								duration: 200,
								complete: function() {
									completed ++;
								}
							});
						};

						/**
						* @method finalPos2
						* Finds the final position of the slot
						*/
						Slot.prototype.finalPos2 = function() {
							var el = this.el,
								el_id,
								pos,
								posMin = 2000000000,
								best,
								bgPos,
								i,
								j,
								k;

							el_id = $(el).attr('id');
							pos = document.getElementById(el_id).style.backgroundPosition;
							pos = pos.split(' ')[1];
							pos = parseInt(pos, 10);

							for(i = 0; i < posArr.length; i++) {
								for(j = 0;;j++) {
									k = posArr[i] + (imgHeight * j);
									if(k > pos) {
										if((k - pos) < posMin) {
											posMin = k - pos;
											//best = k;
											// 0	np cran
											// 323	bamboo
											// 646	cash
											// 969	np mc
											// 1292	oranges
											// 1615	b&c
											// 1938	dice
											// 2261	cabana
											// 2584	palms
											best = <?php echo combo_slot_reel($split_combo[1]); ?>; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
											//this.pos = posArr[i]; //update the final position of the slot
											this.pos = <?php echo combo_slot_reel($split_combo[1]); ?>; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
										}
										break;
									}
								}
							}

							best += imgHeight + 4;
							bgPos = "0 " + best + "px";
							$(el).animate({
								backgroundPosition:"(" + bgPos + ")"
							}, {
								duration: 200,
								complete: function() {
									completed ++;
								}
							});
						};

						/**
						* @method finalPos3
						* Finds the final position of the slot
						*/
						Slot.prototype.finalPos3 = function() {
							var el = this.el,
								el_id,
								pos,
								posMin = 2000000000,
								best,
								bgPos,
								i,
								j,
								k;

							el_id = $(el).attr('id');
							pos = document.getElementById(el_id).style.backgroundPosition;
							pos = pos.split(' ')[1];
							pos = parseInt(pos, 10);

							for(i = 0; i < posArr.length; i++) {
								for(j = 0;;j++) {
									k = posArr[i] + (imgHeight * j);
									if(k > pos) {
										if((k - pos) < posMin) {
											posMin = k - pos;
											//best = k;
											// 0	np cran
											// 323	bamboo
											// 646	cash
											// 969	np mc
											// 1292	oranges
											// 1615	b&c
											// 1938	dice
											// 2261	cabana
											// 2584	palms
											best = <?php echo combo_slot_reel($split_combo[2]); ?>; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
											//this.pos = posArr[i]; //update the final position of the slot
											this.pos = <?php echo combo_slot_reel($split_combo[2]); ?>; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
										}
										break;
									}
								}
							}

							best += imgHeight + 4;
							bgPos = "0 " + best + "px";
							$(el).animate({
								backgroundPosition:"(" + bgPos + ")"
							}, {
								duration: 200,
								complete: function() {
									completed ++;
								}
							});
						};

						/**
						* @method finalPos4
						* Finds the final position of the slot
						*/
						Slot.prototype.finalPos4 = function() {
							var el = this.el,
								el_id,
								pos,
								posMin = 2000000000,
								best,
								bgPos,
								i,
								j,
								k;

							el_id = $(el).attr('id');
							pos = document.getElementById(el_id).style.backgroundPosition;
							pos = pos.split(' ')[1];
							pos = parseInt(pos, 10);

							for(i = 0; i < posArr.length; i++) {
								for(j = 0;;j++) {
									k = posArr[i] + (imgHeight * j);
									if(k > pos) {
										if((k - pos) < posMin) {
											posMin = k - pos;
											//best = k;
											// 0	np cran
											// 323	bamboo
											// 646	cash
											// 969	np mc
											// 1292	oranges
											// 1615	b&c
											// 1938	dice
											// 2261	cabana
											// 2584	palms
											best = <?php echo combo_slot_reel($split_combo[3]); ?>; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
											//this.pos = posArr[i]; //update the final position of the slot
											this.pos = <?php echo combo_slot_reel($split_combo[3]); ?>; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
										}
										break;
									}
								}
							}

							best += imgHeight + 4;
							bgPos = "0 " + best + "px";
							$(el).animate({
								backgroundPosition:"(" + bgPos + ")"
							}, {
								duration: 200,
								complete: function() {
									completed ++;
								}
							});
						};

						/**
						* @method finalPos5
						* Finds the final position of the slot
						*/
						Slot.prototype.finalPos5 = function() {
							var el = this.el,
								el_id,
								pos,
								posMin = 2000000000,
								best,
								bgPos,
								i,
								j,
								k;

							el_id = $(el).attr('id');
							pos = document.getElementById(el_id).style.backgroundPosition;
							pos = pos.split(' ')[1];
							pos = parseInt(pos, 10);

							for(i = 0; i < posArr.length; i++) {
								for(j = 0;;j++) {
									k = posArr[i] + (imgHeight * j);
									if(k > pos) {
										if((k - pos) < posMin) {
											posMin = k - pos;
											//best = k;
											// 0	np cran
											// 323	bamboo
											// 646	cash
											// 969	np mc
											// 1292	oranges
											// 1615	b&c
											// 1938	dice
											// 2261	cabana
											// 2584	palms
											best = <?php echo combo_slot_reel($split_combo[4]); ?>; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
											//this.pos = posArr[i]; //update the final position of the slot
											this.pos = <?php echo combo_slot_reel($split_combo[4]); ?>; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
										}
										break;
									}
								}
							}

							best += imgHeight + 4;
							bgPos = "0 " + best + "px";
							$(el).animate({
								backgroundPosition:"(" + bgPos + ")"
							}, {
								duration: 200,
								complete: function() {
									completed ++;
								}
							});
						};

						/**
						* @method reset
						* Reset a slot to initial state
						*/
						Slot.prototype.reset = function() {
							var el_id = $(this.el).attr('id');
							$._spritely.instances[el_id].t = 0;
							$(this.el).css('background-position', '0px 4px');
							this.speed = 0;
							completed = 0;
							$('#result').html('');
						};

						function enableControl() {
							$('#control').attr("disabled", false);
						}

						function disableControl() {
							$('#control').attr("disabled", true);
						}

						function printResult() { // THIS GROUP DETERMINES WHAT THE RESULTS OUTPUT WILL BE!
							var res;
							res = '<?php echo $message; ?>' + '<br><a href="winner.php?p=<?php echo $final_prize['prize_id']; ?>" style="color: red;">Click here</a> to claim your prize!<a href="winner.php?p=<?php echo $final_prize['prize_id']; ?>"><div id="arrow-popup"></div></a>';
							$('#result').html(res);
						}

						//create slot objects
						// Slot(el, max, step) el = ID Name Of Slot; max = Max Speed; step = Speed Increase Rate;
						var a = new Slot('#slot1', 25, 7),
							b = new Slot('#slot2', 30, 6),
							c = new Slot('#slot3', 35, 5),
							d = new Slot('#slot4', 40, 4),
							e = new Slot('#slot5', 45, 3);

						/**
						* Slot machine controller
						*/
						var x;
						a.start1();
						b.start2();
						c.start3();
						d.start4();
						e.start5();
						$("#control").text("STOP");

						disableControl(); //disable control until the slots reach max speed

						//check every 100ms if slots have reached max speed 
						//if so, enable the control
						x = window.setInterval(function() {
							if(a.speed >= a.maxSpeed && b.speed >= b.maxSpeed && c.speed >= c.maxSpeed && d.speed >= d.maxSpeed && e.speed >= e.maxSpeed) {
								enableControl();
								window.clearInterval(x);
							}
						}, 100);

						$('#control').click(function() {
							if(this.innerHTML == "STOP") {
								a.stop1();
								b.stop2();
								c.stop3();
								d.stop4();
								e.stop5();

								disableControl(); //disable control until the slots stop

								//check every 100ms if slots have stopped
								//if so, enable the control
								x = window.setInterval(function() {
									if(a.speed === 0 && b.speed === 0 && c.speed === 0 && d.speed === 0 && e.speed === 0 && completed === 5) {
										enableControl();
										window.clearInterval(x);
										printResult();
									}
								}, 100);
							}
						});
					}
				}
			});
		}
	</script>
</body>
</html>