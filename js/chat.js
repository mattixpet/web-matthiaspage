(function(){

'use strict';

// simple javascript chatbox
// i wrote it :D

var phpURL = '/php/chatbox.php';
var chatsend = document.getElementById('chatsend');
var chatmsg = document.getElementById('chatmsg');
var chatname = document.getElementById('chatname');

document.addEventListener('DOMContentLoaded', function (e){
	displayChat();
	chatsend.addEventListener('click', updateChat);
	chatmsg.addEventListener('keypress', updateOnEnter);
	chatname.addEventListener('keypress', updateOnEnter);
});

// get everything from our logs database and display
function displayChat() {
	var chat = document.getElementById('chatcontent');
	chat.textContent = ''; // clear text which is there already, in order to add everything again if needed.

	var xhr = new XMLHttpRequest();
	xhr.open("GET", phpURL);
	xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
	xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {

			console.log('hello');

			console.log(this.responseText);

			var data = JSON.parse(this.responseText);

			console.log(data);

			// never show more than 50 messages ago
			var datalen = data.length;
			if (datalen > 50) {
				var start = datalen - 50;
			} else {
				var start = 0;
			}
			for (var i = start; i < datalen; i++) {
				var row = data[i];
				var newP = document.createElement("p");
				var textNode = document.createTextNode(row['name'] + ': ' + row['message']);
				newP.appendChild(textNode);
				chat.appendChild(newP);
			}
			// scroll to bottom of chat, thanks http://stackoverflow.com/questions/270612/scroll-to-bottom-of-div
			chat.scrollTop = chat.scrollHeight;
		}
	};
	xhr.send();
};

function updateChat() {
	var msg = {};
	msg['name'] = chatname.value;
	msg['message'] = chatmsg.value;
	msg['date'] = Math.floor(Date.now()/1000); // get seconds since 1970 unix time
	// validation LOL
	if (msg['name'].length === 0) msg['name'] = 'anonymouse'+Math.floor(Math.random()*10000); // "unique" anon default name!
	if (msg['message'].length === 0) return;
	// end LOL
	console.log(msg);

	// Send a post request!
	var xhr = new XMLHttpRequest();
	xhr.open("POST", phpURL);
	xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
	xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			displayChat();
			// put name back and clear message box
			chatname.value = msg['name'];
			chatmsg.value = '';
		}
	};
	xhr.send(JSON.stringify(msg));
};

// update chat also on enter, not just mouse click on "send"
function updateOnEnter(e){
	var enterKey = 13;
	if (e.which == enterKey){
		updateChat();
		// now clear the message box!
		chatmsg.value = '';
	}
};

})();