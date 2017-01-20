/**
* Slot machine
* Author: Saurabh Odhyan | http://odhyan.com
*
* Licensed under the Creative Commons Attribution-ShareAlike License, Version 3.0 (the "License")
* You may obtain a copy of the License at
* http://creativecommons.org/licenses/by-sa/3.0/
*
* Date: May 23, 2011 
*/
$(document).ready(function() {
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
	* @method stop
	* Stops a slot
	*/
	Slot.prototype.stop = function() {
		var _this = this,
			limit = 20;
		clearInterval(_this.si);
		_this.si = window.setInterval(function() {
			if(_this.speed > limit) {
				_this.speed -= _this.step;
				$(_this.el).spSpeed(_this.speed);
			}
			if(_this.speed <= limit) {
				_this.finalPos(_this.el);
				$(_this.el).spSpeed(0);
				$(_this.el).spStop();
				clearInterval(_this.si);
				$(_this.el).removeClass('motion1');
				$(_this.el).removeClass('motion2');
				$(_this.el).removeClass('motion3');
				$(_this.el).removeClass('motion4');
				$(_this.el).removeClass('motion5');
				_this.speed = 0;
			}
		}, 100);
	};

	/**
	* @method finalPos
	* Finds the final position of the slot
	*/
	Slot.prototype.finalPos = function() {
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
						best = 2584; // MUST BE PRESET TO GET THE ROLLERS TO STOP ON THE RIGHT ONE
						//this.pos = posArr[i]; //update the final position of the slot
						this.pos = 2584; // MUST BE PRESET TO GET OUTCOME TO DISPLAY PROPERLY
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
		if(win[a.pos] === win[b.pos] && win[b.pos] === win[c.pos] && win[c.pos] === win[d.pos] && win[d.pos] === win[e.pos]) {
			switch(win[a.pos]) {
				case 1:
					res = "North Park - Cranberry!";
					break;
				case 2:
					res = "Bamboo Bar!";
					break;
				case 3:
					res = "Cash Bags!";
					break;
				case 4:
					res = "North Park - McCandless!";
					break;
				case 5:
					res = "Oranges!";
					break;
				case 6:
					res = "Bonnie & Clydes!";
					break;
				case 7:
					res = "Dice!";
					break;
				case 8:
					res = "Cabana Bar!";
					break;
				case 9:
					res = "Palm Trees!";
					break;
			}
		} else {
			res = "You Lost This Time. Try Again Next Week!";
		}
		$('#result').html(res);
	}

	//create slot objects
	// Slot(el, max, step) el = ID Name Of Slot; max = Max Speed; step = Speed Increase Rate;
	var a = new Slot('#slot1', 20, 5),
		b = new Slot('#slot2', 25, 4),
		c = new Slot('#slot3', 30, 3),
		d = new Slot('#slot4', 35, 2),
		e = new Slot('#slot5', 40, 1);

	/**
	* Slot machine controller
	*/
	$('#control').click(function() {
		var x;
		if(this.innerHTML == "SPIN") {
			a.start1();
			b.start2();
			c.start3();
			d.start4();
			e.start5();
			this.innerHTML = "STOP";

			disableControl(); //disable control until the slots reach max speed

			//check every 100ms if slots have reached max speed 
			//if so, enable the control
			x = window.setInterval(function() {
				if(a.speed >= a.maxSpeed && b.speed >= b.maxSpeed && c.speed >= c.maxSpeed && d.speed >= d.maxSpeed && e.speed >= e.maxSpeed) {
					enableControl();
					window.clearInterval(x);
				}
			}, 100);
		} else if(this.innerHTML == "STOP") {
			a.stop();
			b.stop();
			c.stop();
			d.stop();
			e.stop();

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
		} else { //reset
			a.reset();
			b.reset();
			c.reset();
			d.reset();
			e.reset();
			this.innerHTML = "SPIN";
		}
	});
});