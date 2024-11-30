document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeSearch();
    initializeModals();
    initializeRescheduleForm();
});

function initializeTabs() {
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const sections = ['newTab', 'scheduledTab', 'rescheduledTab', 'availabilityTab'];
            sections.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.style.display = 'none';
                }
            });
            
            const tabId = this.dataset.tab + 'Tab';
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }
        });
    });
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.appointment-row').forEach(row => {
                const patientName = row.dataset.patientName?.toLowerCase() || '';
                row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }
}

function initializeModals() {
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            hideModal(event.target.id);
        }
    });

    // Close button functionality
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => hideModal());
    });
}

function initializeRescheduleForm() {
    const form = document.getElementById('rescheduleForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const appointmentId = document.getElementById('rescheduleAppointmentId')?.value;
            const newDate = document.getElementById('newDate')?.value;
            const newTime = document.getElementById('newTime')?.value;

            if (appointmentId && newDate && newTime) {
                updateAppointmentStatus(appointmentId, 'Rescheduled', newDate, newTime);
            } else {
                alert('Please fill in all required fields.');
            }
        });
    }
}

// Appointment Actions
function confirmAppointment(appointmentId) {
    if (!appointmentId) return;
    if (confirm('Are you sure you want to confirm this appointment?')) {
        updateAppointmentStatus(appointmentId, 'Scheduled');
    }
}

function completeAppointment(appointmentId) {
    if (!appointmentId) return;
    if (confirm('Are you sure you want to mark this appointment as completed?')) {
        updateAppointmentStatus(appointmentId, 'Completed');
    }
}

function cancelAppointment(appointmentId) {
    if (!appointmentId) return;
    if (confirm('Are you sure you want to cancel this appointment?')) {
        updateAppointmentStatus(appointmentId, 'Canceled');
    }
}

function showRescheduleModal(appointmentId) {
    const appointmentIdInput = document.getElementById('rescheduleAppointmentId');
    const modal = document.getElementById('rescheduleModal');
    if (appointmentIdInput && modal) {
        appointmentIdInput.value = appointmentId;
        modal.style.display = 'flex';
    }
}

function updateAppointmentStatus(appointmentId, status, newDate = null, newTime = null) {
    let formData = new URLSearchParams();
    formData.append('appointment_id', appointmentId);
    formData.append('status', status);
    
    if (newDate && newTime) {
        formData.append('new_date', newDate);
        formData.append('new_time', newTime);
    }

    fetch('update_appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update appointment status.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Availability Management
function showAddAvailabilityForm() {
    const modal = document.getElementById('availabilityModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function hideModal(modalId = null) {
    if (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    } else {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
}

function deleteAvailability(availabilityId) {
    if (!availabilityId) return;
    
    if (confirm('Are you sure you want to delete this availability slot?')) {
        const formData = new URLSearchParams();
        formData.append('action', 'delete_availability');
        formData.append('availability_id', availabilityId);

        fetch('appointments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete availability slot.');
        });
    }
}