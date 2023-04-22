(function(){

'use strict';

// send our sneaky analytics !!! (no ips though, very softcore)
function sendAnalytics () {
    fetch('https://ipinfo.io/json')
    .then(function (response){
        if (response.ok) {
            return response.json();
        }
    })
    .then(function (data) {
        postToDb({
            'type': 'webpageload', 
            'country': (data && data.country) ? data.country : 'Unknown country', 
            'city': (data && data.city) ? data.city : 'Unknown city', 
            'user_agent': window.navigator.userAgent,
            'url': window.location.href,
        });
    })
    .catch(function () {
        // if we can't get country/city from ipcheck, let's still count it as a pageload/save the user agent
        postToDb({
            'type': 'webpageload', 
            'country': 'Unknown country', 
            'city': 'Unknown city', 
            'user_agent': window.navigator.userAgent,
            'url': window.location.href,
        });
    });
}

// Simple function to post to our database, through php/analytics.php
//
// [[data]] must have a very specific format. Possible formats are:
//
// {
//     'type':'webpageload',
//     'country':country,
//     'city':city,
//     'user_agent':user_agent,
//     'url':url (e.g. matthiaspetursson.com/works)
// }
function postToDb(data) {
    console.log('Sending data of type: ' + data.type + ' to server.');

    fetch('/php/analytics.php', {
        method: 'POST',
        mode: 'same-origin',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
}

let debug = false;
if (debug !== true) {
    document.addEventListener('DOMContentLoaded', function(){
        sendAnalytics();
    });
}

})();
