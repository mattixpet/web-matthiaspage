// Player class

(function () {

'use strict';

// imports
var consts = global.get('consts');
var draw = global.get('draw');
var Entity = global.get('class/Entity');

function Player(posX, posY) {
	this.sprite = global.get('imageHandler').getSprite('player');
	this.x = posX;
	this.y = posY;
	this.speedX = 0.2;
	this.speedY = 0.0;
	this.accelerationY = 0.001; // gravity
	// collision width/height
	this.width = this.sprite.getWidth();
	this.height = this.sprite.getHeight();
	this.onGround = false; // start in air
}

Player.prototype = new Entity();

Player.prototype.draw = function () {
	this.sprite.draw(this.x, this.y, 0);

	if (consts.drawBoundingBoxes) {
		draw.drawBox(global.get('ctx'), this.x, this.y, this.width, this.height, 'red');
	}
};

Player.prototype.update = function (dt) {
	var keys = global.get('keys');

	var nextX = this.x;
	if (keys[consts.KEY_RIGHT] || keys[consts.KEY_D]) {
		nextX = this.x + Math.floor(this.speedX * dt);
	} 
	if (keys[consts.KEY_LEFT] || keys[consts.KEY_A]) {
		nextX = this.x - Math.floor(this.speedX * dt);
	}

	var nextY = this.y;
	if (consts.snakeMode) {
		// if gravity is not already off, take it off now, no gravity in snake mode !
		consts.gravity = false;
		this.speedY = this.speedX;
		if (keys[consts.KEY_UP] || keys[consts.KEY_W]) {
			nextY = this.y - Math.floor(this.speedY * dt);
		} 
		if (keys[consts.KEY_DOWN] || keys[consts.KEY_S]) {
			nextY = this.y + Math.floor(this.speedY * dt);
		}
	}

	// effects of gravity
	// displacement = speed * time + 1/2 * acceleration * time squared
	if (consts.gravity) {
		nextY = this.y + Math.floor(this.speedY * dt + this.accelerationY * Math.pow(dt, 2));
	}
	if (!this.isColliding(nextX, nextY)) {
		this.x = nextX;
		this.y = nextY;

		// update speedY with acceleration
		if (!this.onGround && consts.gravity) {
			this.speedY = this.speedY + this.accelerationY * dt;
		}
	} else if (consts.gravity) {
		// if a case of landing set a flag
		if (this.y < nextY) {
			this.onGround = true;
		}

		this.speedY = 0;
	}
};

global.set('class/Player', Player); // export

}());
