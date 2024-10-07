function openEditModal(userId) {
    // Fetch user data from the server
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_user.php?user_id=" + userId, true);
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const user = JSON.parse(xhr.responseText);
            // Populate the form with user data
            document.getElementById('editUserId').value = user.user_id;
            document.getElementById('editFirstName').value = user.user_firstname;
            document.getElementById('editLastName').value = user.user_lastname;
            document.getElementById('editTel').value = user.user_tel;
            document.getElementById('editRole').value = user.user_role;
            document.getElementById('editEmail').value = user.user_email;

            // Show the modal
            $('#editUserModal').modal('show');
        }
    };
    
    xhr.send();
}

// Handle the edit form submission
document.getElementById('editUserForm').onsubmit = function(e) {
    e.preventDefault(); // Prevent page refresh

    const userId = document.getElementById('editUserId').value;
    const firstName = document.getElementById('editFirstName').value;
    const lastName = document.getElementById('editLastName').value;
    const tel = document.getElementById('editTel').value;
    const role = document.getElementById('editRole').value;
    const email = document.getElementById('editEmail').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_user.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert('User updated successfully!');
            location.reload(); // Reload page to show updated user list
        }
    };

    xhr.send("user_id=" + encodeURIComponent(userId) +
            "&firstName=" + encodeURIComponent(firstName) +
            "&lastName=" + encodeURIComponent(lastName) +
            "&tel=" + encodeURIComponent(tel) +
            "&role=" + encodeURIComponent(role) +
            "&email=" + encodeURIComponent(email));
};
