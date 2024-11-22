
    const weekEventsContainer = document.getElementById('week-events');
    const currentWeekElement = document.getElementById('current-week');
    let currentDate = new Date("2024-11-22");

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

        // Afficher la semaine actuelle
        currentWeekElement.textContent = `Semaine du ${startOfWeek.toLocaleDateString()} au ${endOfWeek.toLocaleDateString()}`;

        // Filtrer les événements dans cette semaine
        const filteredEvents = events.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate >= startOfWeek && eventDate <= endOfWeek;
        });

        // Mettre à jour les événements affichés
        weekEventsContainer.innerHTML = filteredEvents.map(event => `
            <div class="event">
                <strong>${event.time}</strong> - ${event.summary}
            </div>
        `).join('') || '<p>Aucun événement pour cette semaine.</p>';
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