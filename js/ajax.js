'use strict';

function ajaxRequest(type, url, callback, data = null) {
    let xhr = new XMLHttpRequest();
    xhr.open(type, url);

    if (!(data instanceof FormData)) {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                console.log('Response Text:', xhr.responseText); // Debugging line
                let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                callback(response);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                console.error('Response Text:', xhr.responseText);
            }
        } else {
            httpErrors(xhr.status);
        }
    };

    xhr.onerror = () => {
        console.error('Network error');
    };

    xhr.send(data);
}

function httpErrors(errorCode) {
    let messages = {
        400: 'Bad Request',
        401: 'Unauthorized',
        403: 'Forbidden',
        404: 'Not Found',
        500: 'Internal Server Error',
        503: 'Service Unavailable'
    };

    if (errorCode in messages) {
        document.getElementById('errors').innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <strong>' + messages[errorCode] + '</strong>';
        document.getElementById('errors').style.display = 'block';
    }
}