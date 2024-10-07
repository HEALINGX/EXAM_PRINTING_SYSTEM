function searchSubject() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('userTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const tdSubNameTH = tr[i].getElementsByTagName('td')[1];
        const tdSubNameEN = tr[i].getElementsByTagName('td')[2];
        let match = false;

        if (tdSubNameTH && tdSubNameEN) {
            const txtValueTH = tdSubNameTH.textContent || tdSubNameTH.innerText;
            const txtValueEN = tdSubNameEN.textContent || tdSubNameEN.innerText;
            if (txtValueTH.toLowerCase().indexOf(filter) > -1 || txtValueEN.toLowerCase().indexOf(filter) > -1) {
                match = true;
            }
        }
        tr[i].style.display = match ? "" : "none";
    }
}
