document.addEventListener("DOMContentLoaded", function() {
    const dropdownTriggerView = document.querySelector('.ribbon-trigger.dropView');
    const dropdownTriggerMenu = document.querySelector('.ribbon-trigger.dropMenu');
    const dropdownContentView = document.querySelector('.dropdown-content.dropView');
    const dropdownContentMenu = document.querySelector('.dropdown-content.dropMenu');

    dropdownTriggerView.addEventListener('click', function() {
        dropdownContentView.classList.toggle('show');
        dropdownContentMenu.classList.remove('show');
    });

    dropdownTriggerMenu.addEventListener('click', function() {
        dropdownContentMenu.classList.toggle('show');
        dropdownContentView.classList.remove('show');
    });
});