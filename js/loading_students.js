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

                        let radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = 'selected-student'; // All radios share the same name to allow only one selection
                        radio.value = student.Id_Membre;
                        studentContainer.appendChild(radio);

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
                                console.log("Selected student:", student.Id_Membre);
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