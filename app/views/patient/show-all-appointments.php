<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - MedCeylon</title>
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

            <a href="<?php echo $basePath; ?>/home  " class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <!-- Header - Kept exactly as original -->
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
<!-- All Appointments-->
 <section class="appointment-section">
    <div class="Section-header">
        <h2>All Appointments</h2>
        <table>
<thead>
    <tr>
        <th>Doctor</th>
        <th>Specialty</th>
        <th>Date</th>
        <th>Time</th>
    </tr>
</thead>
<tbody>
    <?php if ($appointments && $appointments->num_rows>0): ?>
        <?php while($appointments = $appointments->fetch_assoc()): ?>
            <tr>
                <td>Dr. <?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['specialization'] ?? 'General'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['consultation_type']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($appointment['appointment_status']); ?>">
                                            <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                        </span>
                                    </td>
            </tr>
            <?php endwhile; ?>
            </tr>
            <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No appointments found</td>
                            </tr>
                        <?php endif; ?>
</tbody>
        </table>
    </div>
 </section>

        </main>
    </div>


</body>
</html>

<div class="action-buttons">

    <a href="<?php echo $basePath; ?>/patient/show-all-appointments" class="action-btn secondary">
        <i class="ri-calendar-check-line"></i> View All Appointments
    </a>
</div>


<!--Filter Status-->
public function showAllAppointments()
{
    try {
        $patientId = $this->session->getUserId();
        
        
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        
    
        $validStatuses = ['Scheduled', 'Completed', 'Canceled'];
        if (!empty($statusFilter) && !in_array($statusFilter, $validStatuses)) {
            $statusFilter = '';
        }
        

        $appointments = $this->appointmentModel->getPatientAppointmentsByStatus($patientId, $statusFilter);
        
        $data = [
            'appointments' => $appointments,
            'currentFilter' => $statusFilter,
            'basePath' => $this->basePath
        ];

        echo $this->view('patient/show-all-appointments', $data);
        exit();
    } catch (\Exception $e) {
        error_log("Error in showAllAppointments: " . $e->getMessage());
        $this->session->setFlash('error', 'Error loading appointments: ' . $e->getMessage());
        header('Location: ' . $this->url('patient/dashboard'));
        exit();
    }
}

public function getPatientAppointmentsByStatus($patientId, $status = '')
{
    try {
        $query = "SELECT 
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.meet_link,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                s.name as specialization
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE a.patient_id = ?";
                
        if (!empty($status)) {
            $query .= " AND a.appointment_status = ?";
        }
                
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->db->prepare($query);
        
        if (!empty($status)) {
            $stmt->bind_param("is", $patientId, $status);
        } else {
            $stmt->bind_param("i", $patientId);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    } catch (\Exception $e) {
        error_log("Error in getPatientAppointmentsByStatus: " . $e->getMessage());
        throw $e;
    }
}

<div class="filter-controls">
    <form method="GET" action="<?php echo $basePath; ?>/patient/show-all-appointments" class="filter-form">
        <label for="status-filter">Filter by Status:</label>
        <select id="status-filter" name="status" onchange="this.form.submit()">
            <option value="">All Appointments</option>
            <option value="Scheduled" <?php echo ($currentFilter == 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
            <option value="Completed" <?php echo ($currentFilter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
            <option value="Canceled" <?php echo ($currentFilter == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
        </select>
    </form>
</div>

<!--Cancel Appointment-->
public function cancelAppointment()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('patient/show-all-appointments'));
            exit();
        }
        

        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $patientId = $this->session->getUserId();

        if ($appointmentId <= 0) {
            $this->session->setFlash('error', 'Invalid appointment ID');
            header('Location: ' . $this->url('patient/show-all-appointments'));
            exit();
        }

        $appointment = $this->appointmentModel->getById($appointmentId);
        if (!$appointment || $appointment['patient_id'] != $patientId) {
            $this->session->setFlash('error', 'Appointment not found');
            header('Location: ' . $this->url('patient/show-all-appointments'));
            exit();
        }
        

        if ($appointment['appointment_status'] == 'Completed') {
            $this->session->setFlash('error', 'Cannot cancel a completed appointment');
            header('Location: ' . $this->url('patient/show-all-appointments'));
            exit();
        }
        
   
        $result = $this->appointmentModel->updateStatus($appointmentId, 'Canceled');
        
        if ($result) {
            $this->session->setFlash('success', 'Appointment canceled successfully');
        } else {
            $this->session->setFlash('error', 'Failed to cancel appointment');
        }
        
        header('Location: ' . $this->url('patient/show-all-appointments'));
        exit();
        
    } catch (\Exception $e) {
        error_log("Error in cancelAppointment: " . $e->getMessage());
        $this->session->setFlash('error', 'Error canceling appointment: ' . $e->getMessage());
        header('Location: ' . $this->url('patient/show-all-appointments'));
        exit();
    }
}


<?php if ($appointment['appointment_status'] == 'Scheduled'): ?>
    <form method="POST" action="<?php echo $basePath; ?>/patient/cancel-appointment" style="display: inline;">
        <?php if (isset($_SESSION['csrf_token'])): ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php endif; ?>
        <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
        <button type="submit" class="action-btn view-btn" style="background-color: #f44336;" onclick="return confirm('Are you sure you want to cancel this appointment?');">
            <i class="ri-close-line"></i> Cancel
        </button>
    </form>
<?php endif; ?>


<!--Search by Doc Name-->
public function searchAppointments()
{
    try {
        $patientId = $this->session->getUserId();
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

        $searchQuery = htmlspecialchars($searchQuery);
        

        $appointments = $this->appointmentModel->searchPatientAppointments($patientId, $searchQuery);
        
        $data = [
            'appointments' => $appointments,
            'searchQuery' => $searchQuery,
            'basePath' => $this->basePath
        ];

        echo $this->view('patient/show-all-appointments', $data);
        exit();
    } catch (\Exception $e) {
        error_log("Error in searchAppointments: " . $e->getMessage());
        $this->session->setFlash('error', 'Error searching appointments: ' . $e->getMessage());
        header('Location: ' . $this->url('patient/show-all-appointments'));
        exit();
    }
}

public function searchPatientAppointments($patientId, $searchQuery)
{
    try {
        $query = "SELECT 
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.meet_link,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                s.name as specialization
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE a.patient_id = ?";
        
        // Add search condition if query provided
        if (!empty($searchQuery)) {
            $query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR s.name LIKE ?)";
        }
                
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->db->prepare($query);
        
        if (!empty($searchQuery)) {
            $searchParam = "%$searchQuery%";
            $stmt->bind_param("isss", $patientId, $searchParam, $searchParam, $searchParam);
        } else {
            $stmt->bind_param("i", $patientId);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    } catch (\Exception $e) {
        error_log("Error in searchPatientAppointments: " . $e->getMessage());
        throw $e;
    }
}


<div class="search-container">
    <form method="GET" action="<?php echo $basePath; ?>/patient/search-appointments" class="search-form">
        <div class="search-input-group">
            <input type="text" name="search" placeholder="Search doctor or specialty..." 
                value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>">
            <button type="submit" class="search-btn">
                <i class="ri-search-line"></i> Search
            </button>
        </div>
    </form>
</div>


<!--Update Profile-->
public function updateProfile()
{
    try {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('patient/profile'));
            exit();
        }
 
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid security token");
        }
        
        $userId = $this->session->getUserId();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        
    
        if (empty($firstName) || empty($lastName)) {
            throw new \Exception("First name and last name are required");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }
        
        if (!empty($phoneNumber) && !preg_match('/^\+?[0-9]{10,15}$/', $phoneNumber)) {
            throw new \Exception("Invalid phone number format");
        }
        
 
        $this->patientModel->updateProfile($userId, $_POST);
        
        $this->session->setFlash('success', 'Profile updated successfully!');
        header('Location: ' . $this->url('patient/profile'));
        exit();
    } catch (\Exception $e) {
        $this->session->setFlash('error', 'Error updating profile: ' . $e->getMessage());
        header('Location: ' . $this->url('patient/profile'));
        exit();
    }
}

public function updateProfile($userId, $data)
{
    try {
        $this->db->begin_transaction();

        
        $firstName = $this->db->real_escape_string($data['first_name']);
        $lastName = $this->db->real_escape_string($data['last_name']);
        $email = $this->db->real_escape_string($data['email']);
        $phoneNumber = $this->db->real_escape_string($data['phone_number'] ?? '');
        $dob = !empty($data['date_of_birth']) ? $this->db->real_escape_string($data['date_of_birth']) : null;
        $gender = !empty($data['gender']) ? $this->db->real_escape_string($data['gender']) : null;

        $query = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new \Exception("Email already in use by another account");
        }

        $query = "UPDATE users SET 
                 first_name = ?,
                 last_name = ?,
                 email = ?,
                 phone_number = ?,
                 date_of_birth = ?,
                 gender = ?,
                 updated_at = NOW()
                 WHERE user_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssssi", 
            $firstName, 
            $lastName, 
            $email, 
            $phoneNumber, 
            $dob, 
            $gender, 
            $userId
        );
        
        if (!$stmt->execute()) {
            throw new \Exception("Database error: " . $stmt->error);
        }
        
        $this->db->commit();
        return true;
    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("Error in updateProfile: " . $e->getMessage());
        throw $e;
    }
}

