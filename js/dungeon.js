(function(){

'use strict';

// javascript to light up the dungeon nav item on enter the dungeon click

var dungeonLink = document.getElementById('dungeon-link');
if (dungeonLink) {
	dungeonLink.addEventListener('click', function(){
		// toggle red border/ blink
		var id = setInterval(function(){
			document.getElementById('dungeon-nav').classList.toggle('itemFlashing');
		}, 350);

		// stop blinking
		setTimeout(function(){
			clearInterval(id);
			document.getElementById('dungeon-nav').classList.remove('itemFlashing');
		}, 4500);
	});
}

})();
