<!DOCTYPE html>
<html lang="en">
<!-- Header remains the same -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Sidebar remains the same -->
        <aside class="sidebar">
            <div class="logo">
                <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
                    <h1>Medceylon</h1>
                </a>
            </div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item active">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item">
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
            <!-- Header remains the same -->
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <section class="appointments-section">
                <div class="appointments-list">
                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                        <?php while ($appointment = $appointments->fetch_assoc()): ?>
                            <div class="appointment-card" data-appointment-id="<?php echo htmlspecialchars($appointment['appointment_id']); ?>">
                                <div class="appointment-time">
                                    <?php
                                    $time = $appointment['appointment_time'];
                                    if ($time) {
                                        echo htmlspecialchars(date('H:i', strtotime($time)));
                                    } else {
                                        echo htmlspecialchars(date('H:i'));
                                    }
                                    ?>
                                </div>
                                <div class="appointment-info">
                                    <h3>Dr. <?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></h3>
                                    <p><?php echo isset($appointment['specialization']) ? htmlspecialchars($appointment['specialization']) : 'General Medicine'; ?></p>
                                    <p><?php
                                        $date = $appointment['appointment_date'];
                                        echo htmlspecialchars($date ? date('d/m/Y', strtotime($date)) : date('d/m/Y'));
                                        ?></p>
                                    <p><?php echo htmlspecialchars($appointment['hospital_name'] ?? 'Hospital not specified'); ?></p>
                                </div>
                                <div class="status-badge <?php echo strtolower(htmlspecialchars($appointment['appointment_status'])); ?>">
                                    <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                 </div>
                                <div class="appointment-actions">
                                    <button class="action-btn view-details">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-appointments">
                            <p>No appointments found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal remains the same -->
    <div id="appointmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="appointment-details"></div>
        </div>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('appointmentModal');
            const closeBtn = modal.querySelector('.close-btn');

            document.querySelectorAll('.action-btn.view-details').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const appointmentId = this.closest('.appointment-card').dataset.appointmentId;
                    try {
                        const response = await fetch(`${basePath}/patient/get-appointment-details?id=${appointmentId}`);
                        const data = await response.json();

                        modal.querySelector('.appointment-details').innerHTML = `
                           <div class="details-content">
                               <div class="doctor-details">
                                   <h3>Doctor Information</h3>
                                   <p><strong>Name:</strong> Dr. ${data.doctor.first_name} ${data.doctor.last_name}</p>
                                   <p><strong>Specialization:</strong> ${data.specialization}</p>
                                   <p><strong>Hospital:</strong> ${data.hospital}</p>
                               </div>
                               
                               <div class="appointment-details">
                                   <h3>Appointment Information</h3>
                                   <p><strong>Date:</strong> ${data.appointment.date}</p>
                                   <p><strong>Time:</strong> ${data.appointment.time}</p>
                                   <p><strong>Status:</strong> ${data.appointment.status}</p>
                                   <p><strong>Type:</strong> ${data.appointment.consultation_type}</p>
                                   <p><strong>Reason:</strong> ${data.appointment.reason_for_visit}</p>
                               </div>
                           </div>
                       `;
                        modal.style.display = 'flex';
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });

            closeBtn.onclick = () => modal.style.display = 'none';
            window.onclick = (e) => {
                if (e.target === modal) modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>