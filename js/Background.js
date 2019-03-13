// The map/background

(function () {

'use strict';

// imports
var util = global.get('util');
var draw = global.get('draw');
var consts = global.get('consts');
var collision = global.get('collision');

function Background() {
	this.scenes = {}; // scene = page, stage whatever you want to call it, one background image
	this.currentScene = 'clearsky'; // starting scene

	// load collision data for each background image
	this.cData = global.get('background_data');

	// depending on our cData, set the granularity of our grid/ widths etc.
	// split map into bricks, depending on the cData array (>0 is collision brick, 0 is not)
	// see this._drawGrid
	var canvas = global.get('canvas');
	this.gridW = this.cData[this.currentScene][0].length;
	this.gridH = this.cData[this.currentScene].length;
	this.brickWidth = canvas.width / this.gridW;
	this.brickHeight = canvas.height / this.gridH;

	if (!Number.isInteger(this.brickWidth) || !Number.isInteger(this.brickHeight)) {
		util.warn('Brick width or height is not integer, setting to floor.');
		this.brickHeight = Math.floor(this.brickHeight);
		this.brickWidth = Math.floor(this.brickWidth);
	}
}

Background.prototype.draw = function () {
	this._drawBg();
	if (consts.drawBackgroundGrid) {
		this._drawGrid();
	}
};

// dat name tho
Background.prototype._drawBg = function () {
	var imageHandler = global.get('imageHandler');
	imageHandler.getSprite(this.currentScene).draw(0, 0);
};

Background.prototype._drawGrid = function () {
	// iterate over the cData for the current scene, which is a double array
	// with 0 for no collision, >0 for collision
	// e.g. [
	//			[0,0,0,0,0,0],
	//			[0,0,1,1,1,0],
	//			..
	//		]
	// and draw a red box for each place a collision could happen, draw a grid
	// i is x
	// j is y
	var canvas = global.get('canvas');
	var ctx = global.get('ctx');

	var data = this.cData[this.currentScene];
	for (var i = 0; i < this.gridW; i++) {
		for (var j = 0; j < this.gridH; j++) {
			if (data[j][i] !== 0) {
				// collision block, draw it
				var x = Math.floor(i / this.gridW * canvas.width);
				var y = Math.floor(j / this.gridH * canvas.height);
				draw.drawBox(ctx, x, y, this.brickWidth, this.brickHeight, 'red');
			}
		}
	}
};

// is this rectangle colliding with us (the background!)
Background.prototype.isRectangleCollidingWith = function (rX, rY, rW, rH) {
	// check only parts of the grid which are bounded by the rectangle
	var gridTopLeft = util.pixelToGrid(rX, rY, this.gridW, this.gridH);
	var gridBotRight = util.pixelToGrid(rX + rW, rY + rH, this.gridW, this.gridH);
	var data = this.cData[this.currentScene];

	// go from bot right to top left because we assume the most common
	// collision is rectangle with ground
	for (var j = gridBotRight[1]; j >= gridTopLeft[1]; j--) {
		for (var i = gridBotRight[0]; i >= gridTopLeft[0]; i--) {
			if (data[j][i] !== 0) {
				// collision
				return true;
			}
		}
	}

	return false;
};

global.set('class/Background', Background);

}());