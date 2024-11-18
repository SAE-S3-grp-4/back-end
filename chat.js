let websocket;
let chatlogin;
createWebSocket();
function createWebSocket() {
    console.log("En cours de connexion...");
    websocket = new WebSocket("ws://10.20.40.125:12345");
    chatlogin = "Ewen";
}

websocket.onopen = (event) => {
    console.log('Connexion établie.');
}

websocket.onmessage = (event) => {
    console.log('Message reçu : ' + event.data);
    var message = event.data;
    var textArea = $("#chat-room");

    textArea.val(textArea.val() + message + "\n");

    textArea.scrollTop(textArea.prop('scrollHeight'));
}

function sendMessage(event){
    event.stopPropagation();
    var messageInput = $("#chat-message"); // Remplacez 'messageInput' par l'ID de votre champ de saisie de message
    var message = messageInput.val(); // Récupère le message à envoyer

    if (message.trim() !== "") { // Vérifie si le message n'est pas vide ou ne contient que des espaces
        // Envoie le message au serveur de chat en précédant du login
        websocket.send(chatlogin + ": " + message);

        // Efface le champ de saisie après l'envoi du message
        messageInput.val("");
    }
}

document.getElementById('chat-send').addEventListener('submit', (event) =>
{
    event.preventDefault();
    event.stopPropagation();
    sendMessage(event);
});

