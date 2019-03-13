// An object of constats, accessed by consts.val

(function () {

'use strict';

var consts = {};

// input
consts['KEY_W'] = 'W'.charCodeAt(0);
consts['KEY_A'] = 'A'.charCodeAt(0);
consts['KEY_S'] = 'S'.charCodeAt(0);
consts['KEY_D'] = 'D'.charCodeAt(0);
consts['KEY_LEFT'] = 37;
consts['KEY_UP'] = 38;
consts['KEY_RIGHT'] = 39;
consts['KEY_DOWN'] = 40;
consts['KEY_SPACE'] = 32;

// sprites
consts['SPRITEURL'] = 'img/spritesheets/';
consts['BACKGROUNDURL'] = 'img/backgrounds/';

// diagnostics
consts['drawBackgroundGrid'] = true;
consts['drawBoundingBoxes'] = true;
consts['snakeMode'] = false;
consts['gravity'] = true;

global.set('consts', consts);

}());
