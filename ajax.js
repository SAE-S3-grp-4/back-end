'use strict';

function ajaxRequest(type, url, callback, data = null)
{
    let xhr;

    // Create XML HTTP request.
    xhr = new XMLHttpRequest();
    if (type == 'GET' && data != null)
        url += '?' + data;
    xhr.open(type, url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Add the onload function.
    xhr.onload = () =>
    {
        switch (xhr.status)
        {
            case 200:
            case 201:
                console.log(xhr.responseText);
                callback(JSON.parse(xhr.responseText));
                break;
            default:
                httpErrors(xhr.status);
        }
    };

    // Send XML HTTP request.
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
