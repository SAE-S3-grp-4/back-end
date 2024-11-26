const classGroups = {
    'INFO 1': {
        'TD11': ['TP11A', 'TP11B'],
        'TD12': ['TP12C', 'TP12D']
    },
    'INFO 2': {
        'TD21': ['TP21A', 'TP21B'],
        'TD22': ['TP22C', 'TP22D']
    },
    'INFO 3': {
        'TD31': ['TP31A', 'TP31B'],
        'TD32': ['TP32C', 'TP32D']
    },
    'Autre': ['Enseignant']
};

function showStudentPopup(studentId) {
    ajaxRequest('GET', `php/request.php/student/${studentId}`, (student) => {
        // Create the popup container
        let popupContainer = document.createElement('div');
        popupContainer.classList.add('popup-container');

        // Create the popup content
        let popupContent = document.createElement('div');
        popupContent.classList.add('popup-content');

        // Add student information to the popup content
        let studentInfo = `
            <div class="popup-header">
                <button class="close-button">Retour</button>
                <span class="student-grade">${student.Nom_Grade}</span>
            </div>
            <div class="popup-left">
                <img src="path/to/profile/pictures/${student.Pdp_Membre}" alt="Profile Picture" class="profile-picture">
                <h3>${student.Nom_Membre}</h3>
                <p>${student.Nom_Role}</p>
            </div>
            <div class="popup-center">
                <p>Groupe: ${student.Grp_Membre}</p>
                <p>Email: ${student.Mail_Membre}</p>
                <p>Nom: ${student.Nom_Membre}</p>
                <button class="inscriptions-button">Afficher les inscriptions</button>
            </div>
            <div class="popup-footer">
                <button class="delete-button">Supprimer le compte</button>
            </div>
        `;
        popupContent.innerHTML = studentInfo;

        // Add event listener to close button
        popupContent.querySelector('.close-button').addEventListener('click', () => {
            document.body.removeChild(popupContainer);
        });

        // Add event listener to delete button
        popupContent.querySelector('.delete-button').addEventListener('click', () => {
            // Add logic to delete the student account
            console.log("Deleting student:", student.Id_Membre);
        });

        // Add event listener to inscriptions button
        popupContent.querySelector('.inscriptions-button').addEventListener('click', () => {
            // Add logic to display the student's inscriptions
            console.log("Displaying inscriptions for student:", student.Id_Membre);
        });

        // Append the popup content to the popup container
        popupContainer.appendChild(popupContent);

        // Append the popup container to the body
        document.body.appendChild(popupContainer);
    });
}

function loadStudents(students) {
    let container = document.getElementById('dropdown-container');
    container.innerHTML = ''; // Clear existing dropdowns

    // Group students by class group
    let groups = {};
    students.forEach(student => {
        if (!groups[student.Grp_Membre]) {
            groups[student.Grp_Membre] = [];
        }
        groups[student.Grp_Membre].push(student);
    });

    // Create lists for each class group
    for (let info in classGroups) {
        let infoContainer = document.createElement('div');
        infoContainer.classList.add('info-container');

        let infoLabelContainer = document.createElement('div');
        infoLabelContainer.classList.add('label-container');

        let infoLabel = document.createElement('h2');
        infoLabel.textContent = info;
        infoLabelContainer.appendChild(infoLabel);

        let infoToggleIcon = document.createElement('i');
        infoToggleIcon.classList.add('fas', 'fa-chevron-down');
        infoLabelContainer.appendChild(infoToggleIcon);

        infoLabelContainer.addEventListener('click', () => {
            infoContent.classList.toggle('hidden');
            infoToggleIcon.classList.toggle('fa-chevron-down');
            infoToggleIcon.classList.toggle('fa-chevron-up');
        });

        infoContainer.appendChild(infoLabelContainer);

        let infoContent = document.createElement('div');
        infoContent.classList.add('info-content', 'hidden');

        let infoGroups = classGroups[info];
        if (Array.isArray(infoGroups)) {
            // Handle 'Autre' category
            infoGroups.forEach(group => {
                let groupContainer = document.createElement('div');
                groupContainer.classList.add('group-container');

                let labelContainer = document.createElement('div');
                labelContainer.classList.add('label-container');

                let label = document.createElement('h4');
                label.textContent = group;
                labelContainer.appendChild(label);

                let toggleIcon = document.createElement('i');
                toggleIcon.classList.add('fas', 'fa-chevron-down');
                labelContainer.appendChild(toggleIcon);

                labelContainer.addEventListener('click', () => {
                    studentList.classList.toggle('hidden');
                    toggleIcon.classList.toggle('fa-chevron-down');
                    toggleIcon.classList.toggle('fa-chevron-up');
                });

                groupContainer.appendChild(labelContainer);

                let studentList = document.createElement('div');
                studentList.classList.add('student-list', 'hidden');

                if (groups[group]) {
                    groups[group].forEach(student => {
                        let studentContainer = document.createElement('div');
                        studentContainer.classList.add('student-container');

                        let studentName = document.createElement('span');
                        studentName.textContent = student.Nom_Membre;
                        studentContainer.appendChild(studentName);

                        let selectButton = document.createElement('button');
                            selectButton.textContent = "Sélect.";
                            selectButton.classList.add('select-button');

                            selectButton.addEventListener('click', () => {
                                showStudentPopup(student.Id_Membre);
                            });

                        studentContainer.appendChild(selectButton);

                        studentList.appendChild(studentContainer);
                    });
                }

                groupContainer.appendChild(studentList);
                infoContent.appendChild(groupContainer);
            });
        } else {
            for (let td in infoGroups) {
                let tdContainer = document.createElement('div');
                tdContainer.classList.add('td-container');

                let tdLabelContainer = document.createElement('div');
                tdLabelContainer.classList.add('label-container');

                let tdLabel = document.createElement('h3');
                tdLabel.textContent = td;
                tdLabelContainer.appendChild(tdLabel);

                let tdToggleIcon = document.createElement('i');
                tdToggleIcon.classList.add('fas', 'fa-chevron-down');
                tdLabelContainer.appendChild(tdToggleIcon);

                tdLabelContainer.addEventListener('click', () => {
                    tdContent.classList.toggle('hidden');
                    tdToggleIcon.classList.toggle('fa-chevron-down');
                    tdToggleIcon.classList.toggle('fa-chevron-up');
                });

                tdContainer.appendChild(tdLabelContainer);

                let tdContent = document.createElement('div');
                tdContent.classList.add('td-content', 'hidden');

                let tdGroups = infoGroups[td];
                tdGroups.forEach(group => {
                    let groupContainer = document.createElement('div');
                    groupContainer.classList.add('group-container');

                    let labelContainer = document.createElement('div');
                    labelContainer.classList.add('label-container');

                    let label = document.createElement('h4');
                    label.textContent = group;
                    labelContainer.appendChild(label);

                    let toggleIcon = document.createElement('i');
                    toggleIcon.classList.add('fas', 'fa-chevron-down');
                    labelContainer.appendChild(toggleIcon);

                    labelContainer.addEventListener('click', () => {
                        studentList.classList.toggle('hidden');
                        toggleIcon.classList.toggle('fa-chevron-down');
                        toggleIcon.classList.toggle('fa-chevron-up');
                    });

                    groupContainer.appendChild(labelContainer);

                    let studentList = document.createElement('div');
                    studentList.classList.add('student-list', 'hidden');

                    if (groups[group]) {
                        groups[group].forEach(student => {
                            let studentContainer = document.createElement('div');
                            studentContainer.classList.add('student-container');

                            let studentName = document.createElement('span');
                            studentName.textContent = student.Nom_Membre;
                            studentContainer.appendChild(studentName);

                            let selectButton = document.createElement('button');
                            selectButton.textContent = "Sélect.";
                            selectButton.classList.add('select-button');

                            selectButton.addEventListener('click', () => {
                                showStudentPopup(student.Id_Membre);
                            });

                            studentContainer.appendChild(selectButton);


                            studentList.appendChild(studentContainer);
                        });
                    }

                    groupContainer.appendChild(studentList);
                    tdContent.appendChild(groupContainer);
                });

                tdContainer.appendChild(tdContent);
                infoContent.appendChild(tdContainer);
            }
        }

        infoContainer.appendChild(infoContent);
        container.appendChild(infoContainer);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    ajaxRequest('GET', 'php/request.php/students', (students) => {
        console.log('Students loaded:', students);
        loadStudents(students);
    });

    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    const searchStudents = () => {
        const searchTerm = searchInput.value.toLowerCase();
        console.log('Searching for:', searchTerm);
        const studentContainers = document.querySelectorAll('.student-container');
        studentContainers.forEach(container => {
            const studentName = container.querySelector('span').textContent.toLowerCase();
            console.log('Checking student:', studentName);
            if (studentName.includes(searchTerm)) {
                container.style.display = 'flex';
                container.closest('.student-list').classList.remove('hidden'); // Ensure the parent list is visible
                container.closest('.td-content').classList.remove('hidden'); // Ensure the parent list is visible
                container.closest('.info-content').classList.remove('hidden'); // Ensure the parent list is visible
            } else {
                container.style.display = 'none';
            }
        });
    };

    searchButton.addEventListener('click', searchStudents);
    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            searchStudents();
        }
    });
});