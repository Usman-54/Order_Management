document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#employersTable tbody');
    const form = document.querySelector('#addEmployerForm');
    const modalEl = document.getElementById('addEmployerModal');
    let modal = new bootstrap.Modal(modalEl);

    const loadEmployers = () => {
        fetch('getEmployers_data.php')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                data.forEach(emp => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${emp.employer_id}</td>
                        <td>${emp.name}</td>
                        <td>${emp.email}</td>
                        <td>${emp.phone || ''}</td>
                        <td>${emp.address || ''}</td>
                        <td>${emp.role}</td>
                        <td>${emp.created_at}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-edit" data-id="${emp.employer_id}">Edit</button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${emp.employer_id}">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });

                // Delete
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if(confirm('Are you sure?')){
                            const fd = new FormData();
                            fd.append('action','delete');
                            fd.append('employer_id', btn.dataset.id);
                            fetch('getEmployers_data.php', { method:'POST', body: fd })
                                .then(res=>res.json())
                                .then(data=>{
                                    alert(data.message);
                                    loadEmployers();
                                });
                        }
                    });
                });

                // Edit
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const row = btn.closest('tr');
                        form.name.value = row.children[1].textContent;
                        form.email.value = row.children[2].textContent;
                        form.password.value = ''; // optional
                        form.phone.value = row.children[3].textContent;
                        form.address.value = row.children[4].textContent;
                        form.role.value = row.children[5].textContent;
                        form.dataset.editId = btn.dataset.id;
                        modal.show();
                    });
                });
            });
    };

    loadEmployers();

    // Add / Update
    form.addEventListener('submit', e => {
        e.preventDefault();
        const action = form.dataset.editId ? 'update' : 'add';
        const fd = new FormData(form);
        if(action==='update') fd.append('employer_id', form.dataset.editId);
        fd.append('action', action);

        fetch('getEmployers_data.php', { method:'POST', body: fd })
            .then(res=>res.json())
            .then(data=>{
                alert(data.message);
                if(data.success){
                    form.reset();
                    delete form.dataset.editId;
                    modal.hide();
                    loadEmployers();
                }
            });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        form.reset();
        delete form.dataset.editId;
    });
});
