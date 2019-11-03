// Simple function to post emails to our database, through php/emaillist.php

'use strict';

function postEmailToDb(email) {
	console.log('Sending email to server.');

	fetch('php/emaillist.php', {
		method: 'POST',
		mode: 'same-origin',
		headers: {
            'Accept': 'application/json, text/plain, */*',
			'Content-Type': 'application/json'
        },
        body: JSON.stringify(email)
	});
	// .then(function (response) {
	// 	if (response.ok) {
	// 		console.log('Success ! ' + response.status);
	// 		return response.json();
	// 	} else {
	// 		console.log('Error ! ' + response.status);
	// 		return response.json();
	// 	}
	// })
	// .then(function (email) {
	// 	console.log(email);
	// })
	// .catch(function (error) {
	// 	console.log(error);
	// });
}
