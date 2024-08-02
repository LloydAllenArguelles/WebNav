document.addEventListener('DOMContentLoaded', function() {
    function fetchEvents() {
        fetch('includes/fetch_events_ejercito.php')
            .then(response => response.json())
            .then(data => {
                if (data.currentWeek && Array.isArray(data.currentWeek) && data.nextWeek && Array.isArray(data.nextWeek)) {
                    displayEvents(data);
                } else {
                    console.error('Invalid data format:', data);
                }
            })
            .catch(error => console.error('Error fetching events:', error));
    }

    function displayEvents(eventData) {
        const currentWeekContainer = document.getElementById('current-week-schedule');
        const nextWeekContainer = document.getElementById('next-week-schedule');
      
        if (!currentWeekContainer || !nextWeekContainer) {
          console.error('One or both schedule containers not found.');
          return;
        }
      
        // Clear previous content
        currentWeekContainer.innerHTML = '';
        nextWeekContainer.innerHTML = '';
      
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      
        // Get the current day index
        const todayIndex = days.indexOf(new Date().toLocaleDateString('en-US', { weekday: 'long' }));
      
        // Display current week events
        days.forEach((day, index) => {
          if (index >= todayIndex) {
            const currentWeekDayEvents = eventData.currentWeek?.filter(event => event.day === day) || [];
            currentWeekContainer.innerHTML += `
              <tr>
                <td colspan="2"><strong>${day}</strong></td>
              </tr>
              ${currentWeekDayEvents.length ? 
                currentWeekDayEvents.map(event => `
                    <tr>
                      <td>${event.time}</td>
                      <td>${event.event_name} in Room ${event.room_number}</td>
                    </tr>
                `).join('') : 
                `<tr><td colspan="2" class="no-events">No events scheduled</td></tr>`
              }
            `;
          }
        });
      
        // Display next week events
        days.forEach((day, index) => {
          if (index < todayIndex || todayIndex === -1) {
            const nextWeekDayEvents = eventData.nextWeek?.filter(event => event.day === day) || [];
            nextWeekContainer.innerHTML += `
              <tr>
                <td colspan="2"><strong>${day}</strong></td>
              </tr>
              ${nextWeekDayEvents.length ? 
                nextWeekDayEvents.map(event => `
                    <tr>
                      <td>${event.time}</td>
                      <td>${event.event_name} in Room ${event.room_number}</td>
                    </tr>
                `).join('') : 
                `<tr><td colspan="2" class="no-events">No events scheduled</td></tr>`
              }
            `;
          }
        });
      }      

    fetchEvents();
});
