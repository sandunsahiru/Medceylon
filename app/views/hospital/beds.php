<!-- Main Theatre and Beds Management View -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - Theatre & Beds Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.0/main.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <style>
        .calendar-container {
            height: 650px;
            margin-bottom: 20px;
        }
        .resource-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .theatre-card, .bed-card {
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            transition: all 0.3s ease;
        }
        .theatre-card:hover, .bed-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .theatre-card.booked {
            border-left-color: #e74c3c;
        }
        .theatre-card.available {
            border-left-color: #2ecc71;
        }
        .bed-card.occupied {
            border-left-color: #e74c3c;
        }
        .bed-card.available {
            border-left-color: #2ecc71;
        }
        .bed-card.reserved {
            border-left-color: #f39c12;
        }
        .stat-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card .stat-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .stat-card .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-card .stat-label {
            font-size: 14px;
            color: #777;
        }
        .theatre-blue { background-color: #e3f2fd; }
        .theatre-green { background-color: #e8f5e9; }
        .theatre-red { background-color: #ffebee; }
        .theatre-yellow { background-color: #fff8e1; }
        .tab-content {
            padding: 20px 0;
        }
        .fc-event {
            cursor: pointer;
        }
        #searchInput {
            margin-bottom: 15px;
        }
        .legend {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border-radius: 3px;
        }
        .filters {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white p-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-hospital"></i> Hospital Dashboard</h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-md"></i> Dr. Smith
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-light sidebar p-0">
                <nav class="navbar navbar-expand-md navbar-light bg-light flex-md-column">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="sidebarMenu">
                            <ul class="nav flex-column w-100">
                                <li class="nav-item">
                                    <a class="nav-link" href="dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="patients.php">
                                        <i class="fas fa-user-injured"></i> Patients
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="staff.php">
                                        <i class="fas fa-user-md"></i> Staff
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="theatres_beds.php">
                                        <i class="fas fa-procedures"></i> Theatres & Beds
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="appointments.php">
                                        <i class="fas fa-calendar-check"></i> Appointments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="treatments.php">
                                        <i class="fas fa-stethoscope"></i> Treatments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="reports.php">
                                        <i class="fas fa-chart-bar"></i> Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="settings.php">
                                        <i class="fas fa-cog"></i> Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Main content area -->
            <main class="col-md-10 ms-sm-auto px-4 py-3">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h2><i class="fas fa-procedures"></i> Theatre & Beds Management</h2>
                    <div class="btn-toolbar">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addTheatreModal">
                                <i class="fas fa-plus"></i> Add Theatre
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addBedModal">
                                <i class="fas fa-plus"></i> Add Bed
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="printBtn">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>

                <!-- Statistics Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card theatre-blue">
                            <div class="stat-icon text-primary">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="stat-value" id="theatreCount">5</div>
                            <div class="stat-label">Total Theatres</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card theatre-green">
                            <div class="stat-icon text-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value" id="scheduledSurgeries">8</div>
                            <div class="stat-label">Scheduled Surgeries</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card theatre-yellow">
                            <div class="stat-icon text-warning">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div class="stat-value" id="bedCount">32</div>
                            <div class="stat-label">Total Beds</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card theatre-red">
                            <div class="stat-icon text-danger">
                                <i class="fas fa-procedures"></i>
                            </div>
                            <div class="stat-value" id="occupiedBeds">18</div>
                            <div class="stat-label">Occupied Beds</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Tabs -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab">
                            <i class="fas fa-calendar-alt"></i> Calendar View
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="theatres-tab" data-bs-toggle="tab" data-bs-target="#theatres" type="button" role="tab">
                            <i class="fas fa-door-open"></i> Theatres
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="beds-tab" data-bs-toggle="tab" data-bs-target="#beds" type="button" role="tab">
                            <i class="fas fa-bed"></i> Beds
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">
                            <i class="fas fa-clipboard-list"></i> Pending Requests
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <!-- Calendar View Tab -->
                    <div class="tab-pane fade show active" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="calendarSearchInput" placeholder="Search by patient name, doctor, procedure...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend float-end">
                                    <div class="legend-item">
                                        <div class="legend-color bg-primary"></div>
                                        <span>Theatre Booking</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color bg-success"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color bg-warning"></div>
                                        <span>Bed Reserved</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color bg-danger"></div>
                                        <span>Occupied</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-container">
                            <div id="resourceCalendar"></div>
                        </div>
                    </div>

                    <!-- Theatres Tab -->
                    <div class="tab-pane fade" id="theatres" role="tabpanel" aria-labelledby="theatres-tab">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="theatreSearchInput" class="form-control" placeholder="Search theatres...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="filters d-flex justify-content-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary active theatre-filter" data-filter="all">All</button>
                                        <button type="button" class="btn btn-sm btn-outline-success theatre-filter" data-filter="available">Available</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger theatre-filter" data-filter="booked">Booked</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="theatresList">
                            <!-- Theatre cards will be dynamically loaded here -->
                            <div class="col-md-4 mb-3 theatre-item available">
                                <div class="card theatre-card available">
                                    <div class="card-body">
                                        <h5 class="card-title">Theatre 1</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">General Surgery</h6>
                                        <p class="card-text">
                                            <span class="badge bg-success">Available</span>
                                            <small class="text-muted d-block mt-2">Equipped with: Laparoscopic tools, General anesthesia</small>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bookTheatreModal" data-theatre-id="1">
                                                <i class="fas fa-calendar-plus"></i> Book
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#theatreDetailsModal" data-theatre-id="1">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3 theatre-item booked">
                                <div class="card theatre-card booked">
                                    <div class="card-body">
                                        <h5 class="card-title">Theatre 2</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Orthopedic Surgery</h6>
                                        <p class="card-text">
                                            <span class="badge bg-danger">Booked</span>
                                            <small class="text-muted d-block mt-2">Current/Next: Hip Replacement - Dr. Johnson - 10:00 AM</small>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-sm btn-primary" disabled>
                                                <i class="fas fa-calendar-plus"></i> Book
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#theatreDetailsModal" data-theatre-id="2">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- More theatre cards would be here -->
                        </div>
                    </div>

                    <!-- Beds Tab -->
                    <div class="tab-pane fade" id="beds" role="tabpanel" aria-labelledby="beds-tab">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="bedSearchInput" class="form-control" placeholder="Search beds or patients...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="filters d-flex justify-content-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary active bed-filter" data-filter="all">All</button>
                                        <button type="button" class="btn btn-sm btn-outline-success bed-filter" data-filter="available">Available</button>
                                        <button type="button" class="btn btn-sm btn-outline-warning bed-filter" data-filter="reserved">Reserved</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger bed-filter" data-filter="occupied">Occupied</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="bedsList">
                            <!-- Bed cards will be dynamically loaded here -->
                            <div class="col-md-3 mb-3 bed-item available">
                                <div class="card bed-card available">
                                    <div class="card-body">
                                        <h5 class="card-title">Bed 101</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Ward A - General</h6>
                                        <p class="card-text">
                                            <span class="badge bg-success">Available</span>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reserveBedModal" data-bed-id="101">
                                                <i class="fas fa-calendar-plus"></i> Reserve
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bedDetailsModal" data-bed-id="101">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3 bed-item occupied">
                                <div class="card bed-card occupied">
                                    <div class="card-body">
                                        <h5 class="card-title">Bed 102</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Ward A - General</h6>
                                        <p class="card-text">
                                            <span class="badge bg-danger">Occupied</span>
                                            <small class="text-muted d-block mt-2">Patient: John Doe</small>
                                            <small class="text-muted d-block">Until: Apr 29, 2025</small>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateBedModal" data-bed-id="102">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bedDetailsModal" data-bed-id="102">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3 bed-item reserved">
                                <div class="card bed-card reserved">
                                    <div class="card-body">
                                        <h5 class="card-title">Bed 103</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Ward A - General</h6>
                                        <p class="card-text">
                                            <span class="badge bg-warning">Reserved</span>
                                            <small class="text-muted d-block mt-2">Patient: Jane Smith</small>
                                            <small class="text-muted d-block">From: Apr 28, 2025</small>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelReservationModal" data-bed-id="103">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bedDetailsModal" data-bed-id="103">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- More bed cards would be here -->
                        </div>
                    </div>

                    <!-- Pending Requests Tab -->
                    <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Treatment</th>
                                        <th>Requested Date</th>
                                        <th>Doctor</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Robert Brown</td>
                                        <td>Appendectomy</td>
                                        <td>Apr 30, 2025</td>
                                        <td>Dr. Miller</td>
                                        <td><span class="badge bg-warning">Pending Approval</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveRequestModal" data-request-id="1">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectRequestModal" data-request-id="1">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Lisa Taylor</td>
                                        <td>Knee Arthroscopy</td>
                                        <td>May 2, 2025</td>
                                        <td>Dr. Johnson</td>
                                        <td><span class="badge bg-warning">Pending Approval</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveRequestModal" data-request-id="2">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectRequestModal" data-request-id="2">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Michael Wilson</td>
                                        <td>Cataract Surgery</td>
                                        <td>May 5, 2025</td>
                                        <td>Dr. Chen</td>
                                        <td><span class="badge bg-warning">Pending Approval</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveRequestModal" data-request-id="3">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectRequestModal" data-request-id="3">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Theatre Modal -->
    <div class="modal fade" id="addTheatreModal" tabindex="-1" aria-labelledby="addTheatreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTheatreModalLabel">Add New Theatre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTheatreForm">
                        <div class="mb-3">
                            <label for="theatreName" class="form-label">Theatre Name</label>
                            <input type="text" class="form-control" id="theatreName" required>
                        </div>
                        <div class="mb-3">
                            <label for="theatreType" class="form-label">Type/Specialty</label>
                            <select class="form-select" id="theatreType" required>
                                <option value="">Select a specialty</option>
                                <option value="General Surgery">General Surgery</option>
                                <option value="Orthopedic">Orthopedic</option>
                                <option value="Cardiac">Cardiac</option>
                                <option value="Neurosurgery">Neurosurgery</option>
                                <option value="Ophthalmology">Ophthalmology</option>
                                <option value="ENT">ENT</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="theatreEquipment" class="form-label">Equipment</label>
                            <textarea class="form-control" id="theatreEquipment" rows="3" placeholder="List major equipment available in this theatre"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="theatreNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="theatreNotes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTheatreBtn">Save Theatre</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bed Modal -->
    <div class="modal fade" id="addBedModal" tabindex="-1" aria-labelledby="addBedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBedModalLabel">Add New Bed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBedForm">
                        <div class="mb-3">
                            <label for="bedNumber" class="form-label">Bed Number</label>
                            <input type="text" class="form-control" id="bedNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="wardName" class="form-label">Ward</label>
                            <select class="form-select" id="wardName" required>
                                <option value="">Select a ward</option>
                                <option value="General Ward">General Ward</option>
                                <option value="ICU">ICU</option>
                                <option value="Pediatric Ward">Pediatric Ward</option>
                                <option value="Maternity Ward">Maternity Ward</option>
                                <option value="Surgical Ward">Surgical Ward</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bedType" class="form-label">Bed Type</label>
                            <select class="form-select" id="bedType" required>
                                <option value="">Select a bed type</option>
                                <option value="Standard">Standard</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="ICU">ICU</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bedStatus" class="form-label">Status</label>
                            <select class="form-select" id="bedStatus" required>
                                <option value="">Select status</option>
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Reserved">Reserved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bedNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="bedNotes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
