// Booking page functionality
let selectedDoctor = null;

function loadDoctors() {
    fetch('api/doctors.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const doctorSelect = document.getElementById('doctor');
                doctorSelect.innerHTML = '<option value="">-- Choose a Doctor --</option>';
                
                data.data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name} (${doctor.specialization || 'General Practitioner'})`;
                    doctorSelect.appendChild(option);
                });
            }
        });
}

function loadTimeSlots() {
    const doctorId = document.getElementById('doctor').value;
    const appointmentDate = document.getElementById('appointment_date').value;
    
    if (!doctorId || !appointmentDate) {
        return;
    }
    
    fetch(`api/doctors.php?doctor_id=${doctorId}&date=${appointmentDate}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const timeSelect = document.getElementById('appointment_time');
                timeSelect.innerHTML = '<option value="">-- Select Time Slot --</option>';
                
                data.data.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
            }
        });
}

function bookAppointment() {
    fetch('api/auth.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showMessage('Please login to book an appointment', 'error');
                setTimeout(() => window.location.href = 'login.php', 1500);
                return;
            }
            
            const doctorId = document.getElementById('doctor').value;
            const appointmentDate = document.getElementById('appointment_date').value;
            const appointmentTime = document.getElementById('appointment_time').value;
            const reason = document.getElementById('reason').value;
            
            if (!doctorId || !appointmentDate || !appointmentTime) {
                showMessage('Please fill in all required fields', 'error');
                return;
            }
            
            fetch('api/appointments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    doctor_id: doctorId,
                    appointment_date: appointmentDate,
                    appointment_time: appointmentTime,
                    reason: reason
                })
            })
            .then(res => res.json())
            .then(data => {
                const msgDiv = document.getElementById('message');
                if (data.success) {
                    msgDiv.className = 'message success';
                    msgDiv.textContent = 'Appointment booked successfully! You will receive a confirmation email shortly.';
                    msgDiv.style.display = 'block';
                    
                    document.getElementById('booking_form').reset();
                    setTimeout(() => window.location.href = 'my_appointments.php', 2000);
                } else {
                    msgDiv.className = 'message error';
                    msgDiv.textContent = data.error || 'Failed to book appointment';
                    msgDiv.style.display = 'block';
                }
            });
        });
}

function showMessage(message, type) {
    const msgDiv = document.getElementById('message');
    msgDiv.className = `message ${type}`;
    msgDiv.textContent = message;
    msgDiv.style.display = 'block';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    
    const doctorSelect = document.getElementById('doctor');
    const dateInput = document.getElementById('appointment_date');
    
    if (doctorSelect) {
        doctorSelect.addEventListener('change', loadTimeSlots);
    }
    
    if (dateInput) {
        dateInput.addEventListener('change', loadTimeSlots);
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
});
