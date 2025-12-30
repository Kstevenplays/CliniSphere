// Doctors Admin JS
let doctors = [];

function loadDoctors() {
    fetch('../api/admin_doctors.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                doctors = data.data;
                const tbody = document.getElementById('doctors-table-body');
                tbody.innerHTML = '';
                
                data.data.forEach(doctor => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${doctor.first_name} ${doctor.last_name}</td>
                        <td>${doctor.email}</td>
                        <td>${doctor.specialization || 'General Practitioner'}</td>
                        <td>${doctor.availability_status ? 'Active' : 'Inactive'}</td>
                        <td class="actions">
                            <button class="btn btn-success btn-small" onclick="editDoctor(${doctor.id})">Edit</button>
                            <button class="btn btn-danger btn-small" onclick="deleteDoctor(${doctor.id})">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });
}

function openAddDoctorForm() {
    document.getElementById('modal-title').textContent = 'Add Doctor';
    document.getElementById('doc-first-name').value = '';
    document.getElementById('doc-last-name').value = '';
    document.getElementById('doc-email').value = '';
    document.getElementById('doc-specialization').value = '';
    document.getElementById('doctorModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('doctorModal').style.display = 'none';
}

function saveDoctor() {
    const firstName = document.getElementById('doc-first-name').value;
    const lastName = document.getElementById('doc-last-name').value;
    const email = document.getElementById('doc-email').value;
    const specialization = document.getElementById('doc-specialization').value;
    
    if (!firstName || !lastName || !email) {
        alert('Please fill in all required fields');
        return;
    }
    
    fetch('../api/admin_doctors.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            first_name: firstName,
            last_name: lastName,
            email: email,
            specialization: specialization
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Doctor added successfully!');
            closeModal();
            loadDoctors();
        } else {
            alert('Error: ' + (data.error || 'Failed to add doctor'));
        }
    });
}

function editDoctor(doctorId) {
    const doctor = doctors.find(d => d.id === doctorId);
    if (doctor) {
        document.getElementById('modal-title').textContent = 'Edit Doctor';
        document.getElementById('doc-first-name').value = doctor.first_name;
        document.getElementById('doc-last-name').value = doctor.last_name;
        document.getElementById('doc-email').value = doctor.email;
        document.getElementById('doc-specialization').value = doctor.specialization || '';
        document.getElementById('doctorModal').style.display = 'block';
    }
}

function deleteDoctor(doctorId) {
    if (confirm('Are you sure you want to delete this doctor?')) {
        alert('Delete functionality can be implemented in the backend');
    }
}

function logout() {
    window.location.href = '../api/logout.php';
}

document.addEventListener('DOMContentLoaded', loadDoctors);
