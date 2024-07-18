document.addEventListener("DOMContentLoaded", function() {
    const dropdownTriggerView = document.querySelector('.ribbon-trigger.dropView');
    const dropdownTriggerMenu = document.querySelector('.ribbon-trigger.dropMenu');
    const dropdownContentView = document.querySelector('.dropdown-content.dropView');
    const dropdownContentMenu = document.querySelector('.dropdown-content.dropMenu');

    dropdownTriggerView.addEventListener('click', function() {
        if (dropdownContentView.style.display === 'block') {
            dropdownContentView.style.removeProperty('display');
        } else {
            dropdownContentView.style.display = 'block';
        }
    });

    dropdownTriggerMenu.addEventListener('click', function() {
        if (dropdownContentMenu.style.display === 'block') {
            dropdownContentMenu.style.removeProperty('display');
        } else {
            dropdownContentMenu.style.display = 'block';
        }
    });
});