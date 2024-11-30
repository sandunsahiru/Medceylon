<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f0f2f5;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #248c7f;
            color: #ecf0f1;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin: 10px 0;
        }

        .nav-link {
            text-decoration: none;
            color: #ecf0f1;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-link:hover {
            background-color: #1d766d;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .content-header {
            margin-bottom: 20px;
        }

        .stats-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            flex: 1;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card i {
            font-size: 40px;
            color: #248c7f;
            margin-bottom: 10px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #248c7f;
            color: #fff;
        }

        .btn {
            padding: 8px 12px;
            background-color: #248c7f;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1d766d;
        }

        /* Hidden Sections */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .profile-details {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .profile-info {
            flex: 2;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-actions {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-actions button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .profile-actions .edit-btn {
            background-color: #248c7f;
            color: white;
        }

        .profile-actions .edit-btn:hover {
            background-color: #1d766d;
        }

        .profile-actions .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .profile-actions .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Dashboard</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="dashboard">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="requests">
                        <i class="fas fa-tasks"></i> Requests
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="chat">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="doctors">
                        <i class="fas fa-user-md"></i> Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="profile">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Welcome, Caregiver</h1>
                <p>Track your tasks, communicate with patients, and stay on top of caregiving responsibilities.</p>
            </header>

            <section id="dashboard" class="content-section active">
                <div class="stats-cards">
                    <div class="card">
                        <i class="fas fa-tasks"></i>
                        <h3>Requests</h3>
                        <p>15 Pending</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-comments"></i>
                        <h3>Messages</h3>
                        <p>10 Active Chats</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-user-md"></i>
                        <h3>Doctors</h3>
                        <p>3 Connected</p>
                    </div>
                </div>
            </section>

            <section id="requests" class="content-section">
                <h2>Caregiving Requests</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Request Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Daily Checkup</td>
                            <td>Pending</td>
                            <td><button class="btn">View</button></td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Medication Assistance</td>
                            <td>In Progress</td>
                            <td><button class="btn">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section id="chat" class="content-section">
                <h2>Chat with Patients</h2>
                <div class="chat-container">
                    <div class="chat-box">
                        <p><strong>John Doe:</strong> I need help with my medications.</p>
                        <textarea placeholder="Type your reply here..."></textarea>
                    </div>
                    <div class="chat-box">
                        <p><strong>Jane Smith:</strong> Can we schedule a check-in tomorrow?</p>
                        <textarea placeholder="Type your reply here..."></textarea>
                    </div>
                </div>
            </section>

            <section id="doctors" class="content-section">
                <h2>Connected Doctors</h2>
                <ul class="doctor-list">
                    <li>Dr. Alice Brown - Cardiologist</li>
                    <li>Dr. Bob Smith - Neurologist</li>
                    <li>Dr. Clara Johnson - General Physician</li>
                </ul>
            </section>

            <section id="profile" class="content-section">
                <h2>Your Profile</h2>
                <div class="profile-details">
                    <div class="profile-info">
                        <h3>Caregiver Information</h3>
                        <p><strong>Name:</strong> Alex Johnson</p>
                        <p><strong>Email:</strong> alex.johnson@example.com</p>
                        <p><strong>Phone:</strong> +1 234 567 890</p>
                        <p><strong>Experience:</strong> 5 years</p>
                    </div>
                    <div class="profile-actions">
                        <button class="edit-btn">Edit Profile</button>
                        <button class="delete-btn">Delete Account</button>
                    </div>
                </div>
            </section>

            <section id="logout" class="content-section">
                <h2>Logout</h2>
                <p>You have been logged out successfully.</p>
            </section>
        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const links = document.querySelectorAll(".nav-link");
            const sections = document.querySelectorAll(".content-section");

            links.forEach(link => {
                link.addEventListener("click", event => {
                    event.preventDefault();

                    // Toggle Active Link
                    links.forEach(l => l.classList.remove("active"));
                    link.classList.add("active");

                    // Show Corresponding Section
                    const sectionId = link.getAttribute("data-section");
                    sections.forEach(section => {
                        section.classList.remove("active");
                        if (section.id === sectionId) {
                            section.classList.add("active");
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
