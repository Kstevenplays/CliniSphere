// Admin Dashboard JS
let currentAppointmentId = null;

function loadPendingAppointments() {
    fetch('../api/admin_appointments.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('appointments-table-body');
                tbody.innerHTML = '';
                
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7">No pending appointments</td></tr>';
                    return;
                }
                
                data.data.forEach(appointment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${appointment.patient_first_name} ${appointment.patient_last_name}</td>
                        <td>Dr. ${appointment.doctor_first_name} ${appointment.doctor_last_name}</td>
                        <td>${new Date(appointment.appointment_date).toLocaleDateString()}</td>
                        <td>${appointment.appointment_time}</td>
                        <td>${appointment.reason_for_visit || 'N/A'}</td>
                        <td>${new Date(appointment.created_at).toLocaleDateString()}</td>
                        <td class="actions">
                            <button class="btn btn-success" onclick="openApprovalModal(${appointment.id})">Review</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });
}

function openApprovalModal(appointmentId) {
    currentAppointmentId = appointmentId;
    document.getElementById('modal-notes').value = '';
    document.getElementById('approvalModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('approvalModal').style.display = 'none';
    currentAppointmentId = null;
}

function approveAppointment() {
    if (!currentAppointmentId) return;
    
    const notes = document.getElementById('modal-notes').value;
    
    fetch('../api/admin_appointments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            appointment_id: currentAppointmentId,
            action: 'approve',
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Appointment approved! Confirmation email sent to patient.');
            closeModal();
            loadPendingAppointments();
        } else {
            alert('Error: ' + (data.error || 'Failed to approve appointment'));
        }
    });
}

function rejectAppointment() {
    if (!currentAppointmentId) return;
    
    const notes = document.getElementById('modal-notes').value;
    
    fetch('../api/admin_appointments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            appointment_id: currentAppointmentId,
            action: 'reject',
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Appointment rejected! Notification email sent to patient.');
            closeModal();
            loadPendingAppointments();
        } else {
            alert('Error: ' + (data.error || 'Failed to reject appointment'));
        }
    });
}

function logout() {
    window.location.href = '../api/logout.php';
}

// Load appointments on page load
document.addEventListener('DOMContentLoaded', loadPendingAppointments);
