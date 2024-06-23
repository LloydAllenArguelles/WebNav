document.addEventListener('DOMContentLoaded', function() {
    // Mock db
    const userProfile = {
        name: "Student Name",
        studentNumber: "20210000",
        program: "BS Information Technology",
        yearLevel: "3rd Year",
        status: "Regular"
    };

    // temporary lang yung nasa html, once may db na ito yung mga kukunin to get those infos sa user
    document.getElementById('display-name').innerText = `Name: ${userProfile.name}`;
    document.getElementById('display-student-number').innerText = `Student Number: ${userProfile.studentNumber}`;
    document.getElementById('display-program').innerText = `Program: ${userProfile.program}`;
    document.getElementById('display-year-level').innerText = `Year Level: ${userProfile.yearLevel}`;
    document.getElementById('display-status').innerText = `Status: ${userProfile.status}`;
});
