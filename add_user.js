document.getElementById('addUserForm').onsubmit = function(e) {
    e.preventDefault(); // ป้องกันการรีเฟรชหน้า

    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const tel = document.getElementById('tel').value;
    const role = document.getElementById('role').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_user.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // รีเฟรชตารางผู้ใช้หรือแสดงข้อความสำเร็จ
            alert('User added successfully!');
            location.reload(); // โหลดหน้าใหม่เพื่อแสดงผู้ใช้ที่เพิ่ม
        }
    };

    xhr.send("firstName=" + encodeURIComponent(firstName) +
            "&lastName=" + encodeURIComponent(lastName) +
            "&tel=" + encodeURIComponent(tel) +
            "&role=" + encodeURIComponent(role) +
            "&email=" + encodeURIComponent(email) +
            "&password=" + encodeURIComponent(password));
};