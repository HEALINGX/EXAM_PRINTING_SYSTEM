function searchSubject() {
    // Get the search input value
    var input = document.getElementById("searchInput").value.toLowerCase();

    // Get the table and all the rows
    var table = document.getElementById("userTable");
    var rows = table.getElementsByTagName("tr");

    // Loop through all rows except the first one (header)
    for (var i = 1; i < rows.length; i++) {
        var subjectId = rows[i].getElementsByTagName("td")[0];
        var subjectNameTH = rows[i].getElementsByTagName("td")[1];
        var subjectNameEN = rows[i].getElementsByTagName("td")[2];

        if (subjectId || subjectNameTH || subjectNameEN) {
            var idText = subjectId.textContent.toLowerCase();
            var nameTHText = subjectNameTH.textContent.toLowerCase();
            var nameENText = subjectNameEN.textContent.toLowerCase();

            // Check if the input matches the subject ID or name (TH/EN)
            if (idText.includes(input) || nameTHText.includes(input) || nameENText.includes(input)) {
                rows[i].style.display = ""; // Show the row
            } else {
                rows[i].style.display = "none"; // Hide the row
            }
        }
    }
}
