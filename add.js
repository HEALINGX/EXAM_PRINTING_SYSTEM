document.getElementById('addEFMForm').onsubmit = function(e) {
    e.preventDefault(); // Prevent page refresh

    // Get form values
    const subNameEN = document.getElementById('subject').value;
    const examDate = document.getElementById('date').value;
    const examRoom = document.getElementById('room').value;
    const examStart = document.getElementById('time').value;
    const examEnd = document.getElementById('end_time').value; // Ensure to use the correct ID
    const userFirstName = document.getElementById('teacher').value;

    // Create a new XMLHttpRequest object
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Set up a function to handle the response
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Alert the user of success and refresh the page to show updated data
            alert('Exam added successfully!');
            location.reload(); // Reload the page to show the updated exam
        }
    };

    // Send the request with the form data
    xhr.send("sub_nameEN=" + encodeURIComponent(subNameEN) +
              "&exam_date=" + encodeURIComponent(examDate) +
              "&exam_room=" + encodeURIComponent(examRoom) +
              "&exam_start=" + encodeURIComponent(examStart) +
              "&exam_end=" + encodeURIComponent(examEnd) +
              "&user_firstname=" + encodeURIComponent(userFirstName));
};
