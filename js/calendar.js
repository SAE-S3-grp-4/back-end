
    const weekEventsContainer = document.getElementById('week-events');
    const currentWeekElement = document.getElementById('current-week');
    
    const date = new Date();

    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    // This arrangement can be altered based on how we want the date's format to appear.
    let currentDate = new Date(`${year}-${month}-${day}`);

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function getWeekStart(date) {
        const day = date.getDay();
        const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Lundi précédent
        return new Date(date.setDate(diff));
    }

    function getWeekEnd(startDate) {
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + 6); // Dimanche suivant
        return endDate;
    }

    function displayWeekEvents() {
        const startOfWeek = getWeekStart(new Date(currentDate));
        const endOfWeek = getWeekEnd(new Date(startOfWeek));
        currentWeekElement.textContent = `Semaine du ${startOfWeek.toLocaleDateString()} au ${endOfWeek.toLocaleDateString()}`;
    
        // Réinitialiser les événements par jour
        for (let i = 1; i <= 7; i++) {
            document.getElementById(`day-${i}`).querySelector('.events').innerHTML = '';
        }
    
        // Filtrer les événements de la semaine
        const filteredEvents = events.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate >= startOfWeek && eventDate <= endOfWeek;
        });
    
        // Trier les événements par date et heure
        filteredEvents.sort((a, b) => {
            const dateA = new Date(`${a.date}T${a.time}`);
            const dateB = new Date(`${b.date}T${b.time}`);
            return dateA - dateB;
        });
    
        // Ajouter les événements dans les conteneurs correspondants
        filteredEvents.forEach(event => {
            const eventDate = new Date(event.date);
            const dayIndex = (eventDate.getDay() + 6) % 7 + 1; // Convertir 0=Dimanche en 7=Dimanche
            const dayContainer = document.getElementById(`day-${dayIndex}`).querySelector('.events');
    
            // Créer l'élément pour l'événement
            const eventElement = document.createElement('div');
            eventElement.classList.add('event');
            eventElement.textContent = `${event.time} - ${event.summary}`;
    
            // Calcul de la durée (optionnel si vous avez cette information dans l'ICS)
            const startHour = parseInt(event.time.split(':')[0]);
            const endHour = startHour + (event.duration || 1); // Durée par défaut : 1h
    
            // Appliquer le style optionnel de hauteur
            eventElement.style.minHeight = `${(endHour - startHour) * 50}px`; // Chaque heure = 50px
    
            dayContainer.appendChild(eventElement);
        });
    
        // Si aucun événement, afficher un message par jour
        for (let i = 1; i <= 7; i++) {
            const dayContainer = document.getElementById(`day-${i}`).querySelector('.events');
            if (!dayContainer.hasChildNodes()) {
                dayContainer.innerHTML = '<p>Aucun événement.</p>';
            }
        }
    }
    
    // Gestion des flèches de navigation
    document.getElementById('prev-week').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() - 7); // Reculer d'une semaine
        displayWeekEvents();
    });

    document.getElementById('next-week').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() + 7); // Avancer d'une semaine
        displayWeekEvents();
    });

    // Initialisation
    displayWeekEvents();