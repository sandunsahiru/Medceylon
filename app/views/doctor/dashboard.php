<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <h1>Dashboard</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search" id="searchInput">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stats-card patients-overview">
            <div class="stats-header">
                <h2><?php echo $stats['total_patients'] ?? 0; ?></h2>
                <p>Patients</p>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-group-line"></i>
                <div class="stats-info">
                    <h3>All Patients</h3>
                    <p><?php echo $stats['total_patients'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-calendar-check-line"></i>
                <div class="stats-info">
                    <h3>All Appointments</h3>
                    <p><?php echo $stats['upcoming_appointments'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Info (remove in production) -->
    <?php if(isset($_GET['debug'])): ?>
    <div style="background: #f8f9fa; border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 4px;">
        <h4>Debug Information</h4>
        <p>Appointments count: <?php echo count($appointments); ?></p>
        <?php if(!empty($appointments)): ?>
            <p>First appointment patient: <?php echo isset($appointments[0]['patient_first_name']) ? $appointments[0]['patient_first_name'] . ' ' . $appointments[0]['patient_last_name'] : 'Name fields not found'; ?></p>
            <p>Available keys in first appointment: <?php echo implode(', ', array_keys($appointments[0])); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

   <!-- Appointments Section -->
<section class="appointments-section">
    <h2>All Appointments</h2>
    
    <?php if (!empty($appointments)): ?>
        <div class="appointments-list">
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-time">
                        <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                    </div>
                    <div class="appointment-info">
                        <h3><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h3>
                        <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                        <span class="status <?php echo strtolower($appointment['appointment_status']); ?>">
                            <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                        </span>
                    </div>
                    <div class="appointment-actions">
                        <button class="action-btn" onclick="viewAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button class="action-btn" onclick="showAppointmentDetails(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-appointments">
            <p>No appointments scheduled</p>
        </div>
    <?php endif; ?>
</section>
</main>

<!-- Appointment Details Modal -->
<div id="appointmentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Appointment Details</h2>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>
        <div id="appointmentContent"></div>
    </div>
</div>

<script>
    const basePath = '<?php echo $basePath; ?>';
    
    function viewAppointment(appointmentId) {
        window.location.href = `${basePath}/doctor/appointments/view/${appointmentId}`;
    }

    function showAppointmentDetails(appointmentId) {
        // Show loading indicator
        document.getElementById('appointmentContent').innerHTML = '<div class="loading">Loading details...</div>';
        document.getElementById('appointmentModal').style.display = 'block';
        
        // Fetch the appointment details
        fetch(`${basePath}/doctor/getAppointmentDetails?appointment_id=${appointmentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received appointment data:', data);
                
                // Check if there's an error in the response
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Build the HTML for the modal
                document.getElementById('appointmentContent').innerHTML = `
                    <div class="appointment-details">
                        <div class="detail-row">
                            <strong>Patient:</strong>
                            <span>${data.first_name || ''} ${data.last_name || ''}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Date:</strong>
                            <span>${data.appointment_date || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Time:</strong>
                            <span>${data.appointment_time || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Type:</strong>
                            <span>${data.consultation_type || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Status:</strong>
                            <span class="status ${(data.appointment_status || '').toLowerCase()}">${data.appointment_status || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Reason:</strong>
                            <p>${data.reason_for_visit || 'Not specified'}</p>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error fetching appointment details:', error);
                document.getElementById('appointmentContent').innerHTML = `
                    <div class="error-message">
                        <p>Error loading appointment details: ${error.message}</p>
                        <p>Please try again or contact support if the problem persists.</p>
                    </div>
                `;
            });
    }

    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.appointment-card').forEach(card => {
            const patientName = card.querySelector('h3').textContent.toLowerCase();
            card.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('appointmentModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<style>
/* Enhanced modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.close-btn {
    font-size: 24px;
    color: #666;
    background: none;
    border: none;
    cursor: pointer;
}

.detail-row {
    margin-bottom: 15px;
}

.detail-row strong {
    display: block;
    color: #666;
    margin-bottom: 5px;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.status.scheduled { background-color: #4CAF50; color: white; }
.status.completed { background-color: #2196F3; color: white; }
.status.canceled { background-color: #f44336; color: white; }
.status.rescheduled { background-color: #FF9800; color: white; }

.loading {
    text-align: center;
    padding: 20px;
    color: #666;
}

.error-message {
    background-color: #ffeded;
    border-left: 4px solid #f44336;
    padding: 15px;
    margin: 10px 0;
    color: #333;
}
</style>