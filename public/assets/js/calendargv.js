document.addEventListener('DOMContentLoaded', () => {
    const calendarDates = document.getElementById('calendar-dates');
    const currentMonth = document.getElementById('current-month');
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const selectedDateInput = document.getElementById('selected_date');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const scheduleContainer = document.getElementById('schedule-list');

    let date = new Date();
    let selectedDate = localStorage.getItem('selectedDate') || selectedDateInput.value; // Initialize selectedDate

    function getStatusForDate(dateStr) {
        const statuses = {
            '2024-08-01': 'available',
            '2024-08-02': 'booked',
            '2024-08-03': 'pending'
        };
        return statuses[dateStr] || 'default';
    }

    function getDayName(dateStr) {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dateObj = new Date(dateStr);
        return days[dateObj.getDay()];
    }

    function renderCalendar() {
        const year = date.getFullYear();
        const month = date.getMonth();

        currentMonth.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;
        calendarDates.innerHTML = '';

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const lastDateOfMonth = new Date(year, month + 1, 0).getDate();

        let calendarHTML = '';
        for (let i = 0; i < firstDayOfMonth; i++) {
            calendarHTML += '<div class="calendar-date empty"></div>';
        }

        for (let day = 1; day <= lastDateOfMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = new Date().toISOString().slice(0, 10) === dateStr ? 'today' : '';
            const isSelected = selectedDate === dateStr ? 'selected' : '';
            const status = getStatusForDate(dateStr);
            calendarHTML += `<div class="calendar-date ${isToday} ${isSelected} ${status}" data-date="${dateStr}">${day}</div>`;
        }

        calendarDates.innerHTML = calendarHTML;
        selectedDateDisplay.textContent = `${selectedDate} (${getDayName(selectedDate)})`;
        console.log('Selected Date: ' + selectedDate);
        console.log('Selected Date Display: ' + selectedDateDisplay.textContent);
    }

    function changeMonth(direction) {
        date.setMonth(date.getMonth() + direction);
        renderCalendar();
    }

    prevMonthBtn.addEventListener('click', () => changeMonth(-1));
    nextMonthBtn.addEventListener('click', () => changeMonth(1));

    calendarDates.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('calendar-date') && target.dataset.date) {
            selectedDate = target.dataset.date; // Update selectedDate here
            selectedDateInput.value = selectedDate;
            localStorage.setItem('selectedDate', selectedDate); // Save selected date to local storage
            renderCalendar();
    
            const selectedStatus = document.getElementById('stat').value;
const url = `/WebNav/public/includes/fetch_schedules_gv.php?date=${encodeURIComponent(selectedDate)}&stat=${encodeURIComponent(selectedStatus)}`;
console.log(`Request URL: ${url}`);

const xhr = new XMLHttpRequest();
xhr.open('GET', url, true);
xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 300) {
        scheduleContainer.innerHTML = xhr.responseText;
    } else {
        console.error(`Failed to load schedule: ${xhr.statusText} (Status: ${xhr.status})`);
    }
};
xhr.onerror = function () {
    console.error('Request error');
};
xhr.send();
        }
    });

    // On page load, if selected date exists in local storage, use it
    if (selectedDate) {
        selectedDateInput.value = selectedDate;
        renderCalendar();

        const url = `/WebNav/public/includes/fetch_schedules_gv.php?date=${encodeURIComponent(selectedDate)}`;
        console.log(`Request URL on load: ${url}`);

        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                scheduleContainer.innerHTML = xhr.responseText;
            } else {
                console.error(`Failed to load schedule: ${xhr.statusText} (Status: ${xhr.status})`);
            }
        };
        xhr.onerror = function () {
            console.error('Request error');
        };
        xhr.send();
    } else {
        renderCalendar();
    }
    const xhr = new XMLHttpRequest();
    const url = '/WebNav/public/includes/fetch_schedules_gv.php'; // Replace with your target PHP script

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
    if (xhr.status === 200) {
        console.log('Building name sent successfully');
        // Handle response from the PHP script (optional)
    } else {
        console.error('Error sending building name');
        console.error('Status:', xhr.status);
        console.error('Response:', xhr.responseText);
    }
    };

    xhr.send('building_name=' + encodeURIComponent(buildingName));
    
});
