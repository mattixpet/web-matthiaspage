(function(){

'use strict';

// javascript to show or hide the highscore

const chevronToggle = document.getElementsByClassName('highscore-toggle')[0];
const chevronDown = document.getElementsByClassName('highscore-down')[0];
const chevronUp = document.getElementsByClassName('highscore-up')[0];
const highscore = document.getElementsByClassName('highscore')[0];

// In case we can't reach php server for highscore, just don't show anything
if (!highscore) {
    const wrapper = document.getElementsByClassName('highscore-wrapper')[0];
    if (wrapper) {
        wrapper.classList.toggle('hidden');
    }
}

if (highscore && chevronToggle && chevronDown && chevronUp) {
    chevronToggle.addEventListener('click', function(){
        chevronDown.classList.toggle('hidden');
        chevronUp.classList.toggle('hidden');
        highscore.classList.toggle('hidden');
    });
}

})();
