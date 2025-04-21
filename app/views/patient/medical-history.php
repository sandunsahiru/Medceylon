<!-- app/views/patient/medical-history.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .tab-navigation {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding: 0 20px;
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1.1em;
            color: #666;
            position: relative;
        }

        .tab-button.active {
            color: var(--primary-color);
            font-weight: 500;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }

        .tab-content {
            display: none;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .upload-section {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .report-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--primary-color);
        }

        .report-type {
            display: inline-block;
            padding: 4px 8px;
            background: #e3f2fd;
            color: var(--primary-color);
            border-radius: 4px;
            font-size: 0.85em;
            margin: 5px 0;
        }

        .report-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .report-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn {
            background: var(--primary-color);
            color: white;
        }

        .delete-btn {
            background: #ff4444;
            color: white;
        }

        .upload-form {
            display: grid;
            gap: 15px;
            max-width: 600px;
        }

        .form-group {
            display: grid;
            gap: 5px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
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
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/chat" class="nav-item">
                    <i class="ri-message-3-line"></i>
                    <span>Chat</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item active">
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Medical Records</h1>
            </header>

            <?php if ($this->session->hasFlash('success')): ?>
                <div class="success-message"><?php echo $this->session->getFlash('success'); ?></div>
            <?php endif; ?>

            <?php if ($this->session->hasFlash('error')): ?>
                <div class="error-message"><?php echo $this->session->getFlash('error'); ?></div>
            <?php endif; ?>

            <div class="tab-navigation">
                <button class="tab-button active" onclick="showTab('history')">Medical History</button>
                <button class="tab-button" onclick="showTab('reports')">Medical Reports</button>
            </div>

            <div id="history-tab" class="tab-content active">
                <div class="medical-records">
                    <!-- [Previous medical history content remains the same] -->
                </div>
            </div>

            <div id="reports-tab" class="tab-content">
                <section class="upload-section">
                    <h2>Upload New Report</h2>
                    <form action="<?php echo $basePath; ?>/patient/upload-medical-report" method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">

                        <div class="form-group">
                            <label for="report_name">Report Name</label>
                            <input type="text" id="report_name" name="report_name" required>
                        </div>

                        <div class="form-group">
                            <label for="report_type">Report Type</label>
                            <select id="report_type" name="report_type" required>
                                <option value="">Select Type</option>
                                <option value="Lab Test">Lab Test</option>
                                <option value="X-Ray">X-Ray</option>
                                <option value="MRI">MRI</option>
                                <option value="CT Scan">CT Scan</option>
                                <option value="Prescription">Prescription</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="report_file">Upload File</label>
                            <input type="file" id="report_file" name="report_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small>Allowed formats: PDF, JPG, PNG, DOC, DOCX (Max size: 5MB)</small>
                        </div>

                        <button type="submit" class="submit-btn">Upload Report</button>
                    </form>
                </section>

                <section class="report-grid">
                    <?php if ($reports && $reports->num_rows > 0): ?>
                        <?php while ($report = $reports->fetch_assoc()): ?>
                            <div class="report-card">
                                <div class="report-header">
                                    <div class="report-title"><?php echo htmlspecialchars($report['report_name']); ?></div>
                                    <div class="report-date"><?php echo date('M d, Y', strtotime($report['upload_date'])); ?></div>
                                </div>

                                <div class="report-type"><?php echo htmlspecialchars($report['report_type']); ?></div>

                                <?php if ($report['description']): ?>
                                    <p><?php echo htmlspecialchars($report['description']); ?></p>
                                <?php endif; ?>

                                <div class="report-actions">
                                    <button onclick="window.open('<?php echo $basePath . '/uploads/medical-reports/' . $report['file_path']; ?>', '_blank')" class="view-btn">
                                        <i class="ri-eye-line"></i> View
                                    </button>
                                    <button onclick="deleteReport(<?php echo $report['report_id']; ?>)" class="delete-btn">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-reports">
                            <i class="ri-file-warning-line" style="font-size: 48px; color: #999;"></i>
                            <p>No medical reports found. Upload your first report using the form above.</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Define basePath in JavaScript
        const basePath = '<?php echo $basePath; ?>';

        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
        }

        function deleteReport(reportId) {
            if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
                fetch(`${basePath}/patient/delete-medical-report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `report_id=${reportId}&csrf_token=<?php echo $this->session->getCSRFToken(); ?>`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting report: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the report.');
                });
            }
        }

        // File size validation
        document.getElementById('report_file').addEventListener('change', function(e) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (this.files[0].size > maxSize) {
                alert('File is too large. Maximum size allowed is 5MB.');
                this.value = '';
            }
        });
    </script>
</body>

</html>