'use strict';

function httpErrors(xhr) {
    console.log(xhr); // Log the xhr object to the console for debugging
    const errorElement = document.getElementById('error');
    if (errorElement) {
        if (xhr.status === 400) {
            errorElement.innerHTML = 'Error 400: Bad Request - The server could not understand the request due to invalid syntax.';
        } else {
            errorElement.innerHTML = `Error: ${xhr.status} - ${xhr.statusText}`;
        }
    } else {
        console.error('Error element not found in the DOM.');
    }
}

function ajaxRequest(type, url, callback, data = null) {
    let xhr = new XMLHttpRequest();
    xhr.open(type, url);

    if (!(data instanceof FormData)) {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                let response = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                callback(response);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                console.error('Response Text:', xhr.responseText);
            }
        } else {
            httpErrors(xhr);
        }
    };

    xhr.onerror = () => {
        console.error('Network error');
    };

    xhr.send(data);
}