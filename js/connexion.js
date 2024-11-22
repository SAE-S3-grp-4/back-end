document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        ajaxRequest('POST', 'php/login.php', function(response) {
            if (response.success) {
                window.location.href = 'accueil.html';
            } else {
                document.getElementById('error-message').innerText = response.error;
                document.getElementById('error-message').style.display = 'block';
            }
        }, formData);
    });
});