document.addEventListener('DOMContentLoaded', function() {
    // Mock db
    const userProfile = {
        name: "Student Name",
        studentNumber: "20210000",
        program: "BS Information Technology",
        yearLevel: "3rd Year",
        status: "Regular"
    };

    // temporary details lang yung nasa html, will update once meron na tayong db
    document.getElementById('display-name').innerText = `Name: ${userProfile.name}`;
    document.getElementById('display-student-number').innerText = `Student Number: ${userProfile.studentNumber}`;
    document.getElementById('display-program').innerText = `Program: ${userProfile.program}`;
    document.getElementById('display-year-level').innerText = `Year Level: ${userProfile.yearLevel}`;
    document.getElementById('display-status').innerText = `Status: ${userProfile.status}`;
});
