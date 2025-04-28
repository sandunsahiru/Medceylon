<!--Save Doctor Notes-->

public function saveAppointmentNotes()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        
        
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit();
        }
        
        $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $doctorNotes = isset($_POST['doctor_notes']) ? $_POST['doctor_notes'] : '';
        
        if (!$appointmentId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
            exit();
        }
        
        
        $userId = $this->validateDoctorSession();
        $doctorId = $this->doctorModel->getDoctorIdByUserId($userId);
        
        
        $appointment = $this->appointmentModel->getDoctorAppointmentById($appointmentId, $doctorId);
        if (!$appointment) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Appointment not found or does not belong to you']);
            exit();
        }
      
        $result = $this->appointmentModel->updateAppointmentNotes($appointmentId, $doctorNotes);
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Notes saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save notes']);
        }
        exit();
    } catch (\Exception $e) {
        error_log("Error in saveAppointmentNotes: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error saving notes: ' . $e->getMessage()]);
        exit();
    }
}

<div class="form-group">
    <label for="diagnosis-<?php echo $appointment['appointment_id']; ?>">Diagnosis <span class="required">*</span></label>
    <textarea id="diagnosis-<?php echo $appointment['appointment_id']; ?>" class="diagnosis form-control" rows="3" placeholder="Enter patient diagnosis..." required></textarea>
</div>

<div class="form-group">
    <label for="treatment-description-<?php echo $appointment['appointment_id']; ?>">Treatment Description <span class="required">*</span></label>
    <textarea id="treatment-description-<?php echo $appointment['appointment_id']; ?>" class="treatment-description form-control" rows="4" placeholder="Describe the recommended treatment plan..." required></textarea>
</div>

<div class="form-group">
    <label for="medications-<?php echo $appointment['appointment_id']; ?>">Medications</label>
    <textarea id="medications-<?php echo $appointment['appointment_id']; ?>" class="medications form-control" rows="3" placeholder="List prescribed medications and dosages..."></textarea>
</div>

<div class="form-group">
    <label for="travel-restrictions-<?php echo $appointment['appointment_id']; ?>">Travel Restrictions</label>
    <select id="travel-restrictions-<?php echo $appointment['appointment_id']; ?>" class="travel-restrictions form-control">
        <option value="None">No Restrictions</option>
        <option value="Can travel, but avoid high altitudes">Can travel, but avoid high altitudes</option>
        <option value="Can travel, but need wheelchair assistance">Can travel, but need wheelchair assistance</option>
        <option value="Can travel with medical escort only">Can travel with medical escort only</option>
        <option value="Limited to short flights only">Limited to short flights only</option>
        <option value="Not fit for air travel at this time">Not fit for air travel at this time</option>
    </select>
</div>

<div class="form-row">
    <div class="form-group half-width">
        <label for="estimated-budget-<?php echo $appointment['appointment_id']; ?>">Estimated Budget (USD) <span class="required">*</span></label>
        <input type="number" id="estimated-budget-<?php echo $appointment['appointment_id']; ?>" class="estimated-budget form-control" placeholder="Enter estimated cost" min="0" required>
    </div>
    
    <div class="form-group half-width">
        <label for="estimated-duration-<?php echo $appointment['appointment_id']; ?>">Estimated Duration (Days) <span class="required">*</span></label>
        <input type="number" id="estimated-duration-<?php echo $appointment['appointment_id']; ?>" class="estimated-duration form-control" placeholder="Enter estimated duration" min="1" required>
    </div>
</div>

<button class="full-width-button create-treatment-plan-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
    <i class="ri-file-list-3-line"></i> Create Treatment Plan
</button>

<div id="treatment-plan-message-<?php echo $appointment['appointment_id']; ?>" class="message-container" style="display: none;"></div>


<script>
  
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.create-treatment-plan-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const appointmentId = this.getAttribute('data-appointment-id');
                const diagnosisEl = document.getElementById(`diagnosis-${appointmentId}`);
                const treatmentDescriptionEl = document.getElementById(`treatment-description-${appointmentId}`);
                const medicationsEl = document.getElementById(`medications-${appointmentId}`);
                const travelRestrictionsEl = document.getElementById(`travel-restrictions-${appointmentId}`);
                const estimatedBudgetEl = document.getElementById(`estimated-budget-${appointmentId}`);
                const estimatedDurationEl = document.getElementById(`estimated-duration-${appointmentId}`);
                const messageContainer = document.getElementById(`treatment-plan-message-${appointmentId}`);
           
                if (!diagnosisEl.value.trim()) {
                    showMessage(messageContainer, 'Please enter a diagnosis', 'error');
                    diagnosisEl.focus();
                    return;
                }
                
                if (!treatmentDescriptionEl.value.trim()) {
                    showMessage(messageContainer, 'Please enter a treatment description', 'error');
                    treatmentDescriptionEl.focus();
                    return;
                }
                
                if (!estimatedBudgetEl.value || parseFloat(estimatedBudgetEl.value) <= 0) {
                    showMessage(messageContainer, 'Please enter a valid estimated budget', 'error');
                    estimatedBudgetEl.focus();
                    return;
                }
                
                if (!estimatedDurationEl.value || parseInt(estimatedDurationEl.value) <= 0) {
                    showMessage(messageContainer, 'Please enter a valid estimated duration', 'error');
                    estimatedDurationEl.focus();
                    return;
                }
                
              
                const formData = new FormData();
                formData.append('csrf_token', '<?php echo $_SESSION["csrf_token"]; ?>');
                formData.append('appointment_id', appointmentId);
                formData.append('diagnosis', diagnosisEl.value);
                formData.append('treatment_description', treatmentDescriptionEl.value);
                formData.append('medications', medicationsEl.value);
                formData.append('travel_restrictions', travelRestrictionsEl.value);
                formData.append('estimated_budget', estimatedBudgetEl.value);
                formData.append('estimated_duration', estimatedDurationEl.value);
                
             
                this.disabled = true;
                this.innerHTML = '<i class="ri-loader-line"></i> Creating...';
                
        
                fetch(`${basePath}/doctor/create-treatment-plan`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(messageContainer, data.message, 'success');
                        
                     
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showMessage(messageContainer, data.message, 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="ri-file-list-3-line"></i> Create Treatment Plan';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(messageContainer, 'An error occurred while creating treatment plan', 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="ri-file-list-3-line"></i> Create Treatment Plan';
                });
            });
        });
        
        
        function showMessage(container, message, type) {
            container.textContent = message;
            container.style.display = 'block';
            
            if (type === 'success') {
                container.className = 'message-container success-message';
            } else {
                container.className = 'message-container error-message';
            }
            
           
            setTimeout(() => {
                container.style.display = 'none';
            }, 5000);
        }
    });
</script>

<style>
    .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .half-width {
        flex: 1;
    }
    
    .required {
        color: #e74c3c;
    }
    
    .message-container {
        margin-top: 15px;
        padding: 10px;
        border-radius: 5px;
    }
    
    .success-message {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<!--Patient Quick Search-->

public function searchPatients()
{
    try {
       
        header('Content-Type: application/json');
        
        $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
        if (empty($searchQuery) || strlen($searchQuery) < 2) {
            echo json_encode(['success' => false, 'message' => 'Search query too short']);
            exit();
        }
       
        $userId = $this->validateDoctorSession();
        $doctorId = $this->doctorModel->getDoctorIdByUserId($userId);
        
        if (!$doctorId) {
            echo json_encode(['success' => false, 'message' => 'Doctor not found']);
            exit();
        }
        
       
        $patients = $this->doctorModel->searchPatients($doctorId, $searchQuery);
        
        echo json_encode([
            'success' => true,
            'patients' => $patients
        ]);
        exit();
    } catch (\Exception $e) {
        error_log("Error in searchPatients: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error searching patients: ' . $e->getMessage()]);
        exit();
    }
}


public function searchPatients($doctorId, $searchQuery)
{
    try {
       
        $searchTerm = '%' . $searchQuery . '%';
        
        $query = "SELECT DISTINCT 
                u.user_id, 
                u.first_name, 
                u.last_name,
                u.email,
                u.phone_number,
                MAX(a.appointment_date) as last_visit
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id 
                WHERE a.doctor_id = ?
                AND u.is_active = 1
                AND (
                    u.first_name LIKE ? OR 
                    u.last_name LIKE ? OR 
                    u.email LIKE ? OR
                    u.phone_number LIKE ?
                )
                GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone_number
                ORDER BY last_visit DESC
                LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issss", $doctorId, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $patients = [];
        
        while ($row = $result->fetch_assoc()) {
            $patients[] = [
                'id' => $row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone_number'],
                'last_visit' => $row['last_visit'] ? date('d/m/Y', strtotime($row['last_visit'])) : 'Never'
            ];
        }
        
        return $patients;
    } catch (\Exception $e) {
        error_log("Error searching patients: " . $e->getMessage());
        return [];
    }
}


<section class="quick-search-section">
    <div class="quick-search-container">
        <h3><i class="ri-search-line"></i> Patient Quick Search</h3>
        <div class="search-form">
            <input type="text" id="patientSearchInput" placeholder="Search patients by name, email or phone..." autocomplete="off">
            <button id="clearSearchBtn" class="clear-search-btn" style="display: none;">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div id="searchResults" class="search-results" style="display: none;"></div>
    </div>
</section>


<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('patientSearchInput');
        const searchResults = document.getElementById('searchResults');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        let searchTimeout;
        
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
            
          
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            
            searchTimeout = setTimeout(() => {

                fetch(`${basePath}/doctor/search-patients?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.patients.length > 0) {
                        
                            let resultsHtml = '<ul>';
                            data.patients.forEach(patient => {
                                resultsHtml += `
                                    <li>
                                        <div class="patient-info">
                                            <strong>${patient.name}</strong>
                                            <span>${patient.email}</span>
                                            ${patient.phone ? `<span>${patient.phone}</span>` : ''}
                                        </div>
                                        <div class="patient-actions">
                                            <a href="${basePath}/doctor/patient-history/${patient.id}" class="action-link">
                                                <i class="ri-file-list-line"></i> History
                                            </a>
                                            <a href="${basePath}/doctor/book-appointment/${patient.id}" class="action-link">
                                                <i class="ri-calendar-line"></i> Book
                                            </a>
                                        </div>
                                    </li>
                                `;
                            });
                            resultsHtml += '</ul>';
                            
                            searchResults.innerHTML = resultsHtml;
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="no-results">No patients found</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchResults.innerHTML = '<div class="no-results error">Error searching patients</div>';
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });
  
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchResults.style.display = 'none';
            this.style.display = 'none';
            searchInput.focus();
        });
        

        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });
    });
</script>

<style>
  
    .quick-search-section {
        margin-bottom: 20px;
    }
    
    .quick-search-container {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .quick-search-container h3 {
        margin: 0 0 10px 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
    }
    
    .quick-search-container h3 i {
        margin-right: 5px;
        color: var(--primary-color, #4AB1A8);
    }
    
    .search-form {
        position: relative;
    }
    
    #patientSearchInput {
        width: 100%;
        padding: 10px 35px 10px 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 0.9rem;
    }
    
    .clear-search-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 0;
        font-size: 1.2rem;
    }
    
    .search-results {
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    
    .search-results ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .search-results li {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    