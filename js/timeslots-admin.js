// Time Slots Admin JS
function loadDoctorsForSlots() {
    fetch('../api/doctors.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const doctorSelect = document.getElementById('slot-doctor');
                doctorSelect.innerHTML = '<option value="">-- Choose a Doctor --</option>';
                
                data.data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
                    doctorSelect.appendChild(option);
                });
            }
        });
}

function addTimeSlot() {
    const doctorId = document.getElementById('slot-doctor').value;
    const date = document.getElementById('slot-date').value;
    const time = document.getElementById('slot-time').value;
    
    if (!doctorId || !date || !time) {
        alert('Please fill in all fields');
        return;
    }
    
    fetch('../api/admin_timeslots.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            doctor_id: doctorId,
            slot_date: date,
            slot_time: time
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Time slot added successfully!');
            document.getElementById('slot-doctor').value = '';
            document.getElementById('slot-date').value = '';
            document.getElementById('slot-time').value = '';
            loadTimeSlots(doctorId, date);
        } else {
            alert('Error: ' + (data.error || 'Failed to add time slot'));
        }
    });
}

function loadTimeSlots(doctorId, date) {
    if (doctorId && date) {
        fetch(`../api/admin_timeslots.php?doctor_id=${doctorId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const slotsList = document.getElementById('slots-list');
                    slotsList.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        slotsList.innerHTML = '<p>No time slots for this date</p>';
                        return;
                    }
                    
                    data.data.forEach(slot => {
                        const slotCard = document.createElement('div');
                        slotCard.style.cssText = 'background: white; padding: 1rem; border-radius: 4px; border-left: 4px solid #3498db;';
                        slotCard.innerHTML = `
                            <p><strong>Time:</strong> ${slot.slot_time}</p>
                            <p><strong>Status:</strong> ${slot.is_available ? 'Available' : 'Booked'}</p>
                        `;
                        slotsList.appendChild(slotCard);
                    });
                }
            });
    }
}

function openAddSlotForm() {
    alert('Use the form above to add time slots');
}

function logout() {
    window.location.href = '../api/logout.php';
}

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('slot-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
    
    loadDoctorsForSlots();
});
