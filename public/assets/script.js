function toggleSidebar() {
    var sidebar = document.getElementById('sidebar-left');
    sidebar.classList.toggle('active');
}

function toggleNotification() {
    var notification = document.getElementById('notification');
    if (notification.innerHTML === 'Notification is OFF') {
        notification.innerHTML = 'Notification is ON';
    } else {
        notification.innerHTML = 'Notification is OFF';
    }
}

function toggleStatus() {
    var status = document.getElementById('status');
    if (status.innerHTML === 'Status: Offline') {
        status.innerHTML = 'Status: Online';
    } else {
        status.innerHTML = 'Status: Offline';
    }
}
