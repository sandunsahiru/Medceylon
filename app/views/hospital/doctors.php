<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Doctors</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search doctors...">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <section class="doctors-section">
        <div class="section-header">
            <h2>All Doctors</h2>
            <div class="section-actions">
                <select id="departmentFilter" class="filter-select">
                    <option value="">All Departments</option>
                    <?php if (isset($departments)): foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>">
                                <?php echo htmlspecialchars($dept['department_name']); ?>
                            </option>
                    <?php endforeach;
                    endif; ?>
                </select>
            </div>
        </div>

        <div class="doctors-list">
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <div class="doctor-card"
                    data-department="<?php echo $doctor['department_id']; ?>"
                    data-status="<?php echo $doctor['is_active'] ? 'active' : 'inactive'; ?>">
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                        <?php if (!empty($doctor['specialization'])): ?>
                            <p class="specialty">
                                <i class="ri-stethoscope-line"></i>
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </p>
                        <?php endif; ?>

                        <p class="department">
                            <i class="ri-hospital-line"></i>
                            <?php echo htmlspecialchars($doctor['department_name']); ?>
                        </p>
                        <p class="contact">
                            <i class="ri-mail-line"></i>
                            <?php echo htmlspecialchars($doctor['email']); ?>
                        </p>
                        <?php if (!empty($doctor['phone_number'])): ?>
                            <p class="phone">
                                <i class="ri-phone-line"></i>
                                <?php echo htmlspecialchars($doctor['phone_number']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($doctor['license_number'])): ?>
                            <p class="license">
                                <i class="ri-profile-line"></i>
                                License: <?php echo htmlspecialchars($doctor['license_number']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="availability-status <?php echo $doctor['is_available'] ? 'available' : 'unavailable'; ?>">
                        <?php echo $doctor['is_available'] ? 'Available' : 'Unavailable'; ?>
                    </div>

                    <div class="doctor-actions">
                        <button class="action-btn view-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="action-btn edit-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="Edit Doctor">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="action-btn schedule-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="Manage Schedule">
                            <i class="ri-calendar-2-line"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>
</div>


<div id="doctorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="close-btn">&times;</button>
            <h2 id="modalTitle">Add Doctor</h2>
        </div>
        <form id="doctorForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="doctor_id" id="doctorId">

            <div class="form-grid">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone_number">
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" id="specialization" name="specialization" required>
                </div>

                <div class="form-group">
                    <label for="licenseNumber">License Number</label>
                    <input type="text" id="licenseNumber" name="license_number" required>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php if (isset($departments)): foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['department_id']; ?>">
                                    <?php echo htmlspecialchars($dept['department_name']); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-save-line"></i>
                    Save Doctor
                </button>
                <button type="button" class="cancel-btn" onclick="closeModal()">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>






<div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close-btn">&times;</button>
                <h2>Doctor Details</h2>
            </div>
            <div id="doctorDetails" class="patient-details">
                
            </div>
        </div>
    </div>

<script>


    const basePath = 'http://localhost/MedCeylon';
    document.addEventListener('DOMContentLoaded', function () {
        
        const doctorModal = document.getElementById('doctorModal');
        const scheduleModal = document.getElementById('scheduleModal');
        const detailsModal = document.getElementById('detailsModal'); 

       
        const doctorForm = document.getElementById('doctorForm');
        const scheduleForm = document.getElementById('scheduleForm');
        const searchInput = document.getElementById('searchInput');
        const departmentFilter = document.getElementById('departmentFilter');

        
        window.closeModal = function() {
            doctorModal.classList.remove('show');
            doctorForm.reset();
        };

        window.closeScheduleModal = function() {
            scheduleModal.classList.remove('show');
            scheduleForm.reset();
        };

        window.closeDetailsModal = function() { 
            detailsModal.classList.remove('show');
        };

        
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const doctorId = this.dataset.id;
                try {
                    const response = await fetch(`${basePath}/hospital/get-doctor-details?id=${doctorId}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    
                    // Populate the details modal
                    document.getElementById('doctorDetails').innerHTML = `
                        <div class="details-section">
                            <h3>Dr. ${data.first_name} ${data.last_name}</h3>
                            <p><i class="ri-mail-line"></i> ${data.email}</p>
                            ${data.phone_number ? `<p><i class="ri-phone-line"></i> ${data.phone_number}</p>` : ''}
                            <p><i class="ri-stethoscope-line"></i> ${data.specialization}</p>
                            <p><i class="ri-hospital-line"></i> ${data.department_name}</p>
                            ${data.license_number ? `<p><i class="ri-profile-line"></i> License: ${data.license_number}</p>` : ''}
                        </div>
                    `;
                    
                    detailsModal.classList.add('show');
                    
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to fetch doctor details: ' + error.message);
                }
            });
        });

        // Edit Doctor Buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const doctorId = this.dataset.id;
                try {
                    const response = await fetch(`${basePath}/hospital/get-doctor-details?id=${doctorId}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();

                    document.getElementById('modalTitle').textContent = 'Edit Doctor';
                    document.getElementById('doctorId').value = data.doctor_id;
                    document.getElementById('firstName').value = data.first_name;
                    document.getElementById('lastName').value = data.last_name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('phone').value = data.phone_number || '';
                    document.getElementById('specialization').value = data.specialization;
                    document.getElementById('licenseNumber').value = data.license_number;
                    document.getElementById('department').value = data.department_id;

                    doctorModal.classList.add('show');
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to fetch doctor details: ' + error.message);
                }
            });
        });

        
        document.querySelectorAll('.schedule-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const doctorId = this.dataset.id;
                try {
                    const response = await fetch(`${basePath}/hospital/get-doctor-schedule?id=${doctorId}`);
                    if (!response.ok) throw new Error('Failed to fetch schedule');
                    const schedule = await response.json();
                    const scheduleHTML = Object.entries(schedule).map(([day, times]) => {
                        const dayName = day.charAt(0).toUpperCase() + day.slice(1);
                        return `<div class="schedule-day"><h4>${dayName}</h4>${times.available ? `<p>${times.start || ''} - ${times.end || ''}</p>` : `<p class="text-danger">Not available</p>`}</div>`;
                    }).join('');
                    document.getElementById('doctorDetails').innerHTML = `<div class="details-section"><h3>Doctor's Weekly Schedule</h3>${scheduleHTML ? `<div class="schedule-grid">${scheduleHTML}</div>` : `<p>No schedule found</p>`}</div>`;
                    detailsModal.classList.add('show');
                } catch (error) {
                    alert(error.message);
                }
            });
        });

        
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal === doctorModal) {
                    window.closeModal();
                } else if (modal === scheduleModal) {
                    window.closeScheduleModal();
                } else if (modal === detailsModal) {
                    window.closeDetailsModal();
                }
            });
        });

        
        window.addEventListener('click', function(event) {
            if (event.target === doctorModal) {
                window.closeModal();
            } else if (event.target === scheduleModal) {
                window.closeScheduleModal();
            } else if (event.target === detailsModal) {
                window.closeDetailsModal();
            }
        });

    
    document.querySelectorAll('.action-btn.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const doctorId = this.dataset.id;
            const csrf_token = document.querySelector('input[name="csrf_token"]').value;
            
            try {
                const response = await fetch(`${basePath}/hospital/toggle-doctor-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `doctor_id=${doctorId}&csrf_token=${csrf_token}`
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    
                    const doctorCard = this.closest('.doctor-card');
                    const isActive = this.dataset.active === '1';
                    this.dataset.active = isActive ? '0' : '1';
                    
                    if (doctorCard) {
                        doctorCard.dataset.status = isActive ? 'inactive' : 'active';
                    }
                    
                    
                    alert('Doctor status updated successfully');
                    
                    location.reload();
                } else {
                    alert(result.error || 'Failed to update doctor status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update doctor status: ' + error.message);
            }
        });
    });

    
    doctorForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        
        try {
            const response = await fetch(`${basePath}/hospital/save-doctor`, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'An error occurred');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save doctor: ' + error.message);
        }
    });

   
    scheduleForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const doctorId = formData.get('doctor_id');
        const csrf_token = formData.get('csrf_token');
        
    
        const scheduleData = {};
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        days.forEach(day => {
            const start = formData.get(`schedule[${day}][start]`);
            const end = formData.get(`schedule[${day}][end]`);
            const available = formData.has(`schedule[${day}][available]`) ? 1 : 0;
            
            if (start || end) {
                scheduleData[day] = {
                    start: start || '',
                    end: end || '',
                    available: available
                };
            }
        });
        
        try {
            const response = await fetch(`${basePath}/hospital/saveDoctorSchedule`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    doctor_id: doctorId,
                    csrf_token: csrf_token,
                    schedule: scheduleData
                })
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert(result.error || 'An error occurred');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save schedule: ' + error.message);
        }
    });

    if (searchInput) {
        searchInput.addEventListener('input', filterDoctors);
    }
    
    if (departmentFilter) {
        departmentFilter.addEventListener('change', filterDoctors);
    }
    
    function filterDoctors() {
        const searchTerm = searchInput.value.toLowerCase();
        const departmentId = departmentFilter.value;
        
        document.querySelectorAll('.doctor-card').forEach(card => {
            const doctorName = card.querySelector('h3').textContent.toLowerCase();
            const doctorDepartment = card.dataset.department;
            
            const matchesSearch = !searchTerm || doctorName.includes(searchTerm);
            const matchesDepartment = !departmentId || doctorDepartment === departmentId;
            
            if (matchesSearch && matchesDepartment) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
});
</script>
</body>

</html>