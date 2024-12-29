


function showStudentPopup(studentId) {
    ajaxRequest('GET', `php/controllerPanelAdmin.php/student/${studentId}`, (student) => {
        let popupContainer = document.createElement('div');
        popupContainer.classList.add('popup-container');

        let popupContent = `
            <div class="popup-content">
                <div class="popup-header">
                    <button class="close-button">Retour</button>
                    <span class="student-grade">Grade : ${student.Nom_Grade}</span>
                </div>
                <div class="popup-body">
                    <div class="popup-left">
                        <img src="img/imgMembre/${student.Pdp_Membre}" alt="Photo de profil" class="profile-picture">
                        <h3>${student.Prenom_Membre} ${student.Nom_Membre}</h3>
                        <p>${student.Nom_Role}</p>
                    </div>
                    <div class="popup-center">
                        <p><b>Groupe : </b>${student.Grp_Membre}</p>
                        <p><b>Email : </b>${student.Mail_Membre}</p>
                        <p><b>Pseudo : </b>${student.Pseudo_Membre}</p>
                        <button class="deconnexion-button">Se déconnecter</button>
                    </div>
                    
        `;
        if (student.Nom_Role === 'Administrateur') {
            popupContent += `
                <div class="popup-bottom">
                    <button class="admin-panel-button" id="admin-panel-button">Accéder au panel Administrateur</button>
                </div>`;
        }
        popupContent += `</div>
                <div class="popup-footer">
                    <button class="delete-button">Supprimer le compte</button>
                </div>
            </div>`;
        console.log(popupContent);
        popupContainer.innerHTML = popupContent;

        if (student.Nom_Role === 'Administrateur') {
            // Accéder au panel administrateur
            popupContainer.querySelector('.admin-panel-button').addEventListener('click', () => {
                window.location.href = 'adminPanel.html';
            });
        }

        // Fermer la page
        popupContainer.querySelector('.close-button').addEventListener('click', () => {
            window.location.href = 'accueil.html';
        });

        // Bouton supprimer
        popupContainer.querySelector('.delete-button').addEventListener('click', () => {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas annuler cette action !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    ajaxRequest('DELETE', `php/controllerPanelAdmin.php/student/${studentId}`, (response) => {
                        if (response.success) {
                            Swal.fire(
                                'Supprimé !',
                                'Le compte a été supprimé.',
                                'success'
                            ).then(() => {
                                document.body.removeChild(popupContainer);
                                ajaxRequest('GET', 'php/controllerPanelAdmin.php/students', (students) => {
                                    loadStudents(students);
                                });
                            });
                        } else {
                            Swal.fire(
                                'Erreur !',
                                'Échec de la suppression du compte : ' + response.error,
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Déconnexion
        popupContainer.querySelector('.deconnexion-button').addEventListener('click', () => {
            ajaxRequest('POST', 'php/controllerProfil.php/deconnexion', (response) => {
                window.location.href = 'accueil.html';
            });

        });

        document.body.appendChild(popupContainer);
    });
}



ajaxRequest('POST', 'php/controllerProfil.php/member-id', (response) => {
    let id_user = response['Id_User'];
    console.log(id_user);
    showStudentPopup(id_user);
}, );



