document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('signup-form').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        ajaxRequest('POST', 'php/inscription.php', function(response) {
            if (response.success) {
                alert('Votre inscription a bien été effectuée.');
                window.location.href = 'accueil.html';
            } else {
                let errorMessageDiv = document.getElementById('error-message');
                errorMessageDiv.innerText = response.error;
                errorMessageDiv.style.display = 'block';
            }
        }, formData);
    });
});