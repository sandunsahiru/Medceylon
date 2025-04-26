<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/user_management.css">
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>


    <div class="main-content">
        <header>
            <h1>User Management</h1>
        </header>

        <body>

            <div class="container">
                <header>
                    <p>Manage your team members and their account permissions here.</p>
                </header>

                <div class="toolbar">
                    <div class="search-box">
                        <input type="text" id="search" placeholder="Search">
                    </div>
                    <div class="user-filters">
                        <button onclick="navigate('doctors')">Doctors</button>
                        <button onclick="navigate('patients')">Patients</button>
                        <button onclick="navigate('hospitals')">Hospitals</button>
                        <button onclick="navigate('caretakers')">Caretakers</button>
                    </div>
                    <div>
                        <!-- <span>All users <strong>44</strong></span> -->

                        <button class="add-user-btn"onclick="window.location.href='<?= $basePath ?>/admin/adduser'">+ Add user</button>
                    </div>
                </div>

                <?php
                // Get page from URL parameter, default to 'doctors'
                $page = $_GET['page'] ?? 'doctors';

                // Depending on the page, call the appropriate method from your Admin model
                switch ($page) {
                    case 'patients':
                        $result = $admin->getPatients();
                        break;
                    case 'hospitals':
                        // Implement getHospitals() in your model and fetch data for hospitals
                        $result = $admin->getHospitals();
                        break;
                    case 'doctors':
                    default:
                        $result = $admin->getDoctors();
                        break;
                }
                ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>User name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Access</th>
                            <th>Last active</th>
                            <th>Date added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="user-list">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <?php if ($page == 'hospitals'): ?>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['contact_number']) ?></td>
                                        <td><?= htmlspecialchars($row['website']) ?></td>

                                    <?php else: ?>
                                        <td>
                                            <!-- img src="./img/user.jpg" alt="<?= $row['first_name'] ?>" class="profile-img"
                                            style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;"-->
                                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['gender']) ?></td>
                                        <td><?= htmlspecialchars($row['age']) ?> years</td>
                                        <td>Patient</td>
                                        <td>Mar 4, 2024</td>
                                        <td>July 4, 2022</td>
                                        <td>
                                            <button class="view-profile" 
                                                onclick="window.location.href='<?= $basePath ?>/admin/editProfile?user_id=<?= $row['user_id'] ?>'">
                                                Edit Profile
                                            </button>

                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No patients found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>



                <!-- <div class="pagination">
                    <button class="page-btn">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">4</button>
                    <button class="page-btn">5</button>
                    <button class="page-btn">6</button>
                </div> -->

                <div class="notification" id="notification">
                    <p>“Amélie Laurent” details updated</p>
                    <a href="#">Undo</a> | <a href="#">View profile</a>
                </div>

            </div>

        </body>

        </html>

        <script>

            function navigate(page) {
                window.location.href = `?page=${page}`;
            }
            function navigateprofile(user_id) {
                window.location.href = `editProfile?user_id=${user_id}`;
            }


            function navigateToAddUser() {
                window.location.href = 'register.php';
            }
        </script>