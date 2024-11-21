'use strict';

function ajaxRequest(type, url, callback, data = null) {
    let xhr = new XMLHttpRequest();
    if (type == 'GET' && data != null) {
        url += '?' + data;
    }
    xhr.open(type, url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = () => {
        try {
            let response = JSON.parse(xhr.responseText);
            callback(response);
        } catch (e) {
            console.error('Invalid JSON response:', xhr.responseText);
            httpErrors(xhr.status);
        }
    };

    xhr.send(data);
}

//------------------------------------------------------------------------------
//--- httpErrors ---------------------------------------------------------------
//------------------------------------------------------------------------------
// Display an error message accordingly to an error code.
// \param errorCode The error code (HTTP status for example).
function httpErrors(errorCode)
{
    let messages =
        {
            400: 'Requête incorrecte',
            401: 'Authentifiez vous',
            403: 'Accès refusé',
            404: 'Page non trouvée',
            500: 'Erreur interne du serveur',
            503: 'Service indisponible'
        };

    // Display error.
    if (errorCode in messages)
    {
        document.getElementById('errors').innerHTML = '<i class="fa-solid ' + 'fa-circle-exclamation"></i> <strong>' + messages[errorCode] + '</strong>';
        document.getElementById('errors').style.display = 'block';
    }
}


