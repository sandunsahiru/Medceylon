<!-- Save this as diagnostic.php in your views directory -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment Plan Diagnostics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            color: #333;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .section {
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #2196F3;
        }
        .btn-secondary:hover {
            background-color: #0b7dda;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .log-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Treatment Plan System Diagnostics</h1>
    
    <div class="section">
        <h2>Database Diagnostics</h2>
        <div class="card">
            <button id="checkDbBtn" class="btn">Check Database Structure</button>
            <button id="testSubmitBtn" class="btn btn-secondary">Test Submit Treatment Plan</button>
            <button id="clearLogBtn" class="btn btn-danger">Clear Log</button>
        </div>
        
        <h3>Diagnostic Log</h3>
        <div id="logContainer" class="log-container"></div>
    </div>
    
    <div class="section">
        <h2>Sample Treatment Plan Data</h2>
        <div class="card">
            <form id="testForm">
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <div style="flex: 1; min-width: 300px;">
                        <label for="session_id">Session ID:</label>
                        <input type="number" id="session_id" name="session_id" value="1" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <label for="appointment_id">Appointment ID:</label>
                        <input type="number" id="appointment_id" name="appointment_id" value="1" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <label for="patient_id">Patient ID:</label>
                        <input type="number" id="patient_id" name="patient_id" value="1" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <label for="travel_restrictions">Travel Restrictions:</label>
                        <input type="text" id="travel_restrictions" name="travel_restrictions" value="None" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                    </div>
                    
                    <div style="flex: 1; min-width: 300px;">
                        <label for="vehicle_type">Vehicle Type:</label>
                        <select id="vehicle_type" name="vehicle_type" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                            <option value="Regular Vehicle">Regular Vehicle</option>
                            <option value="Wheelchair Accessible">Wheelchair Accessible</option>
                            <option value="Ambulance">Ambulance</option>
                        </select>
                        
                        <label for="arrival_deadline">Arrival Deadline:</label>
                        <input type="date" id="arrival_deadline" name="arrival_deadline" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <label for="treatment_description">Treatment Description:</label>
                        <textarea id="treatment_description" name="treatment_description" rows="3" style="width: 100%; padding: 8px; margin-bottom: 10px;">Test treatment description</textarea>
                    </div>
                    
                    <div style="flex: 1; min-width: 300px;">
                        <label for="estimated_budget">Estimated Budget:</label>
                        <input type="number" id="estimated_budget" name="estimated_budget" value="5000" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <label for="estimated_duration">Estimated Duration (days):</label>
                        <input type="number" id="estimated_duration" name="estimated_duration" value="7" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    </div>
                </div>
                
                <button type="button" id="submitTestData" class="btn">Submit Test Data</button>
            </form>
        </div>
    </div>
    
    <div class="section">
        <h2>Query Helper</h2>
        <div class="card">
            <h3>Create Treatment Plans Table</h3>
            <pre>
CREATE TABLE IF NOT EXISTS treatment_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    travel_restrictions VARCHAR(255) DEFAULT 'None',
    vehicle_type VARCHAR(100) DEFAULT 'Regular Vehicle',
    arrival_deadline DATE NULL,
    treatment_description TEXT,
    estimated_budget DECIMAL(10,2) NOT NULL,
    estimated_duration INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    INDEX(session_id),
    INDEX(patient_id),
    INDEX(doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            </pre>
            <button id="copyCreateTable" class="btn btn-secondary">Copy to Clipboard</button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logContainer = document.getElementById('logContainer');
            const checkDbBtn = document.getElementById('checkDbBtn');
            const testSubmitBtn = document.getElementById('testSubmitBtn');
            const clearLogBtn = document.getElementById('clearLogBtn');
            const copyCreateTableBtn = document.getElementById('copyCreateTable');
            const submitTestDataBtn = document.getElementById('submitTestData');
            
            // Helper function to add log entry
            function addLogEntry(message, type = 'info') {
                const logEntry = document.createElement('div');
                logEntry.className = type;
                
                // Add timestamp
                const timestamp = new Date().toLocaleTimeString();
                logEntry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
                
                logContainer.appendChild(logEntry);
                logContainer.scrollTop = logContainer.scrollHeight;
            }
            
            // Check database structure
            checkDbBtn.addEventListener('click', function() {
                addLogEntry('Checking database structure...', 'info');
                
                fetch('<?php echo $basePath; ?>/vpdoctor/diagnoseTreatmentPlansTable')
                    .then(response => response.json())
                    .then(data => {
                        addLogEntry('Database check completed!', 'success');
                        
                        if (data.error) {
                            addLogEntry(`Error: ${data.message}`, 'error');
                            return;
                        }
                        
                        // Display table info
                        if (data.table_info) {
                            const tableInfo = data.table_info;
                            addLogEntry(`Table exists: ${tableInfo.table_exists ? 'Yes' : 'No'}`, tableInfo.table_exists ? 'success' : 'error');
                            
                            if (tableInfo.diagnostics && tableInfo.diagnostics.length > 0) {
                                tableInfo.diagnostics.forEach(diagnostic => {
                                    const type = diagnostic.includes('Error') || diagnostic.includes('Missing') ? 'error' : 'info';
                                    addLogEntry(diagnostic, type);
                                });
                            }
                            
                            // Show columns as a table if they exist
                            if (tableInfo.columns && tableInfo.columns.length > 0) {
                                let tableHTML = '<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
                                
                                tableInfo.columns.forEach(column => {
                                    tableHTML += `<tr>
                                        <td>${column.Field}</td>
                                        <td>${column.Type}</td>
                                        <td>${column.Null}</td>
                                        <td>${column.Key}</td>
                                        <td>${column.Default || 'NULL'}</td>
                                    </tr>`;
                                });
                                
                                tableHTML += '</table>';
                                
                                const tableEntry = document.createElement('div');
                                tableEntry.innerHTML = '<h4>Table Columns:</h4>' + tableHTML;
                                logContainer.appendChild(tableEntry);
                            }
                        }
                        
                        // Display connection info
                        if (data.connection_info) {
                            const connInfo = data.connection_info;
                            addLogEntry(`Connection test: ${connInfo.connection_test}`, 
                                connInfo.connection_test === 'Success' ? 'success' : 'error');
                            
                            if (connInfo.db_error) {
                                addLogEntry(`Database error: ${connInfo.db_error}`, 'error');
                            }
                        }
                        
                        // Display PHP info
                        if (data.php_info) {
                            const phpInfo = data.php_info;
                            addLogEntry(`PHP Version: ${phpInfo.php_version}`);
                            addLogEntry(`MySQL Client Version: ${phpInfo.mysql_client_version}`);
                            addLogEntry(`MySQL Server Version: ${phpInfo.mysql_server_version}`);
                        }
                    })
                    .catch(error => {
                        addLogEntry(`Fetch error: ${error.message}`, 'error');
                    });
            });
            
            // Test submit treatment plan
            testSubmitBtn.addEventListener('click', function() {
                // Create a basic test treatment plan
                const testData = new FormData();
                testData.append('csrf_token', '<?php echo $_SESSION['csrf_token'] ?? ''; ?>');
                testData.append('session_id', '1');
                testData.append('appointment_id', '1');
                testData.append('patient_id', '1');
                testData.append('travel_restrictions', 'None');
                testData.append('vehicle_type', 'Regular Vehicle');
                testData.append('treatment_description', 'Test treatment plan');
                testData.append('estimated_budget', '1000');
                testData.append('estimated_duration', '7');
                
                addLogEntry('Submitting test treatment plan...', 'info');
                
                fetch('<?php echo $basePath; ?>/vpdoctor/createTreatmentPlan', {
                    method: 'POST',
                    body: testData
                })
                .then(response => response.json())
                .then(data => {
                    addLogEntry(`Test submit result: ${data.success ? 'Success' : 'Failed'}`, 
                        data.success ? 'success' : 'error');
                    
                    addLogEntry(`Message: ${data.message}`);
                    
                    if (data.diagnostics && data.diagnostics.length > 0) {
                        addLogEntry('Diagnostics:', 'info');
                        data.diagnostics.forEach((line, index) => {
                            addLogEntry(`  ${index + 1}. ${line}`);
                        });
                    }
                })
                .catch(error => {
                    addLogEntry(`Fetch error: ${error.message}`, 'error');
                });
            });
            
            // Submit test data from form
            submitTestDataBtn.addEventListener('click', function() {
                const form = document.getElementById('testForm');
                const formData = new FormData(form);
                
                addLogEntry('Submitting form data...', 'info');
                
                // Log the data being sent
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                    addLogEntry(`  ${key}: ${value}`, 'info');
                });
                
                fetch('<?php echo $basePath; ?>/vpdoctor/createTreatmentPlan', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    addLogEntry(`Form submit result: ${data.success ? 'Success' : 'Failed'}`, 
                        data.success ? 'success' : 'error');
                    
                    addLogEntry(`Message: ${data.message}`);
                    
                    if (data.diagnostics && data.diagnostics.length > 0) {
                        addLogEntry('Diagnostics:', 'info');
                        data.diagnostics.forEach((line, index) => {
                            addLogEntry(`  ${index + 1}. ${line}`);
                        });
                    }
                })
                .catch(error => {
                    addLogEntry(`Fetch error: ${error.message}`, 'error');
                });
            });
            
            // Clear log
            clearLogBtn.addEventListener('click', function() {
                logContainer.innerHTML = '';
                addLogEntry('Log cleared', 'info');
            });
            
            // Copy create table SQL
            copyCreateTableBtn.addEventListener('click', function() {
                const createTableSql = document.querySelector('pre').textContent;
                navigator.clipboard.writeText(createTableSql)
                    .then(() => {
                        addLogEntry('SQL copied to clipboard!', 'success');
                    })
                    .catch(err => {
                        addLogEntry(`Failed to copy: ${err}`, 'error');
                    });
            });
            
            // Initial log entry
            addLogEntry('Diagnostics page loaded. Ready to test treatment plan functionality.', 'info');
        });
    </script>
</body>
</html>