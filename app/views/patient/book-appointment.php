<!-- app/views/patient/book-appointment.php -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Book Appointment - MediCare</title>
   <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
   <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
   <div class="container">
       <aside class="sidebar">
       <div class="logo">
    <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
        <h1>Medceylon</h1>
    </a>
</div>

<nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item active">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/chat" class="nav-item">
                    <i class="ri-message-3-line"></i>
                    <span>Chat</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/profile" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>
           
           <a href="<?php echo $basePath; ?>/logout" class="exit-button">
               <i class="ri-logout-box-line"></i>
               <span>Exit</span>
           </a>
       </aside>

       <main class="main-content">
           <header class="top-bar">
               <h1>Book Appointment</h1>
           </header>

           <?php if ($this->session->hasFlash('success')): ?>
               <div class="success-message"><?php echo $this->session->getFlash('success'); ?></div>
           <?php endif; ?>

           <?php if ($this->session->hasFlash('error')): ?>
               <div class="error-message"><?php echo $this->session->getFlash('error'); ?></div>
           <?php endif; ?>

           <section class="appointments-section">
               <form id="appointmentForm" action="<?php echo $basePath; ?>/patient/process-appointment" method="POST" enctype="multipart/form-data" class="appointment-form">
                   <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
                   
                   <div class="form-group">
                       <label for="doctor">Select Doctor:</label>
                       <select name="doctor_id" id="doctor" required>
                           <option value="">Select a doctor</option>
                           <?php while($doctor = $doctors->fetch_assoc()): ?>
                               <option value="<?php echo $doctor['doctor_id']; ?>">
                                   Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?> 
                                   (<?php echo htmlspecialchars($doctor['hospital_name']); ?>)
                               </option>
                           <?php endwhile; ?>
                       </select>
                   </div>

                   <div class="form-group">
                       <label for="appointment_date">Select Date:</label>
                       <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                   </div>

                   <div class="form-group">
                       <label for="time_slot">Available Time Slots:</label>
                       <select name="time_slot" id="time_slot" required disabled>
                           <option value="">Select date and doctor first</option>
                       </select>
                   </div>

                   <div class="form-group">
                       <label for="consultation_type">Consultation Type:</label>
                       <select name="consultation_type" id="consultation_type" required>
                           <option value="Online">Online</option>
                           <option value="In-Person">In-Person</option>
                       </select>
                   </div>

                   <div class="form-group">
                       <label for="reason">Reason for Visit:</label>
                       <textarea name="reason" id="reason" required></textarea>
                   </div>

                   <div class="form-group">
                       <label for="medical_history">Medical History (Optional):</label>
                       <textarea name="medical_history" id="medical_history"></textarea>
                   </div>

                   <div class="form-group">
                       <label for="documents">Upload Documents (Optional):</label>
                       <input type="file" name="documents[]" id="documents" multiple accept=".pdf,.jpg,.jpeg,.png">
                       <small>Maximum file size: 5MB per file</small>
                   </div>

                   <button type="submit" class="submit-btn">Book Appointment</button>
               </form>
           </section>
       </main>
   </div>

   <script>
   const basePath = '<?php echo $basePath; ?>';
   
   // Replace the existing script section in book-appointment.php with this updated version
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor');
    const dateInput = document.getElementById('appointment_date');
    const timeSlotSelect = document.getElementById('time_slot');

    async function loadTimeSlots() {
        if (!doctorSelect.value || !dateInput.value) return;

        timeSlotSelect.innerHTML = '<option value="">Loading...</option>';
        timeSlotSelect.disabled = true;

        try {
            const response = await fetch(`${basePath}/patient/get-time-slots`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `doctor_id=${doctorSelect.value}&date=${dateInput.value}`
            });

            const slots = await response.json();
            timeSlotSelect.innerHTML = '';
            timeSlotSelect.disabled = false;

            if (!Array.isArray(slots) || slots.length === 0) {
                timeSlotSelect.innerHTML = '<option value="">No available slots</option>';
                return;
            }

            // First option
            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "Select a time slot";
            timeSlotSelect.appendChild(defaultOption);

            // Add all available time slots
            slots.forEach(slot => {
                const option = document.createElement('option');
                
                // Check if slot is a string or an object
                if (typeof slot === 'string') {
                    option.value = slot;
                    option.textContent = slot;
                } else if (typeof slot === 'object' && slot !== null) {
                    // If it's an object, try to get a suitable property
                    // Adjust based on your actual data structure
                    const value = slot.time || slot.value || JSON.stringify(slot);
                    const display = slot.display || slot.label || value;
                    
                    option.value = value;
                    option.textContent = display;
                }
                
                timeSlotSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error loading time slots:", error);
            timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>';
        }
    }

    doctorSelect.addEventListener('change', loadTimeSlots);
    dateInput.addEventListener('change', loadTimeSlots);

    // Form validation
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        const doctorSelect = document.getElementById('doctor');
        const dateInput = document.getElementById('appointment_date');
        const timeSlotSelect = document.getElementById('time_slot');
        const reasonInput = document.getElementById('reason');

        if (!doctorSelect.value || !dateInput.value || !timeSlotSelect.value || !reasonInput.value) {
            e.preventDefault();
            alert('Please fill all required fields');
        }
    });

    // Document file size validation
    document.getElementById('documents').addEventListener('change', function(e) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        let files = e.target.files;
        
        for(let file of files) {
            if(file.size > maxSize) {
                alert('File ' + file.name + ' is too large. Maximum size is 5MB');
                e.target.value = '';
                return;
            }
        }
    });
});
   </script>
</body>
</html>