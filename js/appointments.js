// Appointments display
function loadAppointments() {
    fetch('api/appointments.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const appointmentsList = document.getElementById('appointments-list');
                
                if (data.data.length === 0) {
                    appointmentsList.innerHTML = '<p>No appointments found. <a href="booking.php">Book one now</a></p>';
                    return;
                }
                
                appointmentsList.innerHTML = '';
                
                data.data.forEach(appointment => {
                    const statusClass = `status-${appointment.status}`;
                    const appointmentCard = document.createElement('div');
                    appointmentCard.className = 'appointment-card';
                    appointmentCard.innerHTML = `
                        <h4>Dr. ${appointment.doctor_first_name} ${appointment.doctor_last_name}</h4>
                        <p><strong>Specialization:</strong> ${appointment.specialization || 'General Practitioner'}</p>
                        <p><strong>Date:</strong> ${new Date(appointment.appointment_date).toLocaleDateString()}</p>
                        <p><strong>Time:</strong> ${appointment.appointment_time}</p>
                        <p><strong>Reason:</strong> ${appointment.reason_for_visit || 'N/A'}</p>
                        <span class="appointment-status ${statusClass}">${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}</span>
                    `;
                    appointmentsList.appendChild(appointmentCard);
                });
            }
        })
        .catch(err => {
            document.getElementById('appointments-list').innerHTML = '<p>Error loading appointments. Please try again.</p>';
        });
}

// Load appointments on page load
document.addEventListener('DOMContentLoaded', loadAppointments);
