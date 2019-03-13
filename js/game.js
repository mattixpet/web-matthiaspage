// My javascript game Dream Of Songs
// Matthias Petursson - oldschool01123@gmail.com

(function () {

'use strict';

// imports
var util = global.get('util');
var LoadingBar = global.get('class/LoadingBar'); // loading bar constructor
var ImageHandler = global.get('class/ImageHandler');
var Player = global.get('class/Player');
var EntityManager = global.get('class/EntityManager');
var Background = global.get('class/Background');
var CollisionManager = global.get('class/CollisionManager');

global.set('canvas', document.getElementById('dreamOfSongs'));
var canvas = global.get('canvas');
global.set('ctx', canvas.getContext('2d')); // context
var ctx = global.get('ctx');
// canvas.width = window.innerWidth;
// canvas.height = window.innerHeight;

function start() {
	var loadingBar = new LoadingBar();
	global.set('loadingBar', loadingBar);

	loadingBar.preloadImage(continueLoading); // have to actually preload the (go figure) loading background
}

function continueLoading() {
	// background image of loading bar should now be loaded
	var loadingBar = global.get('loadingBar');
	loadingBar.start();

	var imageHandler = new ImageHandler(loadingBar);
	global.set('imageHandler', imageHandler);

	// load all images/sprites in game, then invoke initGame callback
	imageHandler.preloadImages(initGame);
}

function begin() {
	// handle input
}

function update(dt) {
	var entityManager = global.get('entityManager');
	for (var key in entityManager.entities) {
		entityManager.entities[key].update(dt);
	}
}

function draw() {
	ctx.clearRect(0, 0, canvas.width, canvas.height);

	var background = global.get('background');
	background.draw();

	var entityManager = global.get('entityManager');
	for (var key in entityManager.entities) {
		entityManager.entities[key].draw();
	}
}

function end(fps, panic) {

	// Display FPS in bottom right corner
	ctx.fillText(fps.toPrecision(4) + ' fps', canvas.width - 55, canvas.height - 10);
	
	if (panic) {
        // This pattern introduces non-deterministic behavior, but in this case
        // it's better than the alternative (the application would look like it
        // was running very quickly until the simulation caught up to real
        // time). See the documentation for `MainLoop.setEnd()` for additional
        // explanation.
        var discardedTime = Math.round(MainLoop.resetFrameDelta());
        util.warn('Main loop panicked, probably because the browser tab was put in the background. Discarding ' + discardedTime + 'ms');
    }
}

function initGame() {
	var entityManager = new EntityManager();
	global.set('entityManager', entityManager);

	var background = new Background();
	global.set('background', background);

	var collisionManager = new CollisionManager();
	global.set('collisionManager', collisionManager);

	var player = new Player(300, 135);
	entityManager.register(player);

	var loadingBar = global.get('loadingBar');
	if (loadingBar.getProgress() !== 1) {
		util.warn('Loading not finished when it should be, setting as finished to continue game.');
		loadingBar.updateProgress(1); // end loading
	}

	//  Start the game !
	MainLoop.setBegin(begin).setUpdate(update).setDraw(draw).setEnd(end).start();
}

start(); // kicks everything off 

}());
