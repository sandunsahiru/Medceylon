<!-- views/book_appointment.php -->
<?php include 'templates/header.php'; ?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/patient_sidebar.php'; ?>

    <div class="content">
        <h1>Book an Appointment</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="formbold-main-wrapper">
            <div class="formbold-form-wrapper">
                <form action="?page=book_appointment" method="POST">
                    <div class="formbold-mb-5">
                        <label for="doctor_id" class="formbold-form-label"> Select Doctor </label>
                        <select name="doctor_id" id="doctor_id" class="formbold-form-input" required>
                            <option value="">-- Select Doctor --</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= htmlspecialchars($doctor['doctor_id']); ?>">
                                    Dr. <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?> - <?= htmlspecialchars($doctor['specialization'] ?: 'General') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex flex-wrap formbold--mx-3">
                        <div class="w-full sm:w-half formbold-px-3">
                            <div class="formbold-mb-5">
                                <label for="appointment_date" class="formbold-form-label"> Date </label>
                                <input
                                    type="date"
                                    name="appointment_date"
                                    id="appointment_date"
                                    class="formbold-form-input"
                                    required
                                    min="<?= date('Y-m-d'); ?>"
                                />
                            </div>
                        </div>
                        <div class="w-full sm:w-half formbold-px-3">
                            <div class="formbold-mb-5">
                                <label for="appointment_time" class="formbold-form-label"> Time </label>
                                <input
                                    type="time"
                                    name="appointment_time"
                                    id="appointment_time"
                                    class="formbold-form-input"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="formbold-mb-5">
                        <label for="reason_for_visit" class="formbold-form-label"> Reason for Visit </label>
                        <textarea
                            name="reason_for_visit"
                            id="reason_for_visit"
                            class="formbold-form-input"
                            rows="4"
                            placeholder="Describe your reason for visit"
                        ></textarea>
                    </div>

                    <div>
                        <button class="formbold-btn">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
