<link rel="stylesheet" href="/Medceylon/public/assets/css/caregiver.css?v=5">

<div class="page-wrapper">
    <div class="container">
        <h2 class="page-title">Caregiver Dashboard</h2>

        <h4 style="text-align: center; margin-bottom: 20px;">
            Your Average Rating: ⭐ <?= htmlspecialchars($averageRating) ?>/5
        </h4>

        <h3>Pending Patient Requests</h3>

        <?php if (empty($pendingRequests)): ?>
            <p class="empty">No pending requests.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="caregiver-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= htmlspecialchars($req['phone_number'] ?? '-') ?></td>
                                <td class="action-buttons">
                                    <form method="POST" action="/Medceylon/caregiver/request/respond/<?= $req['request_id'] ?>">
                                        <button name="status" value="Accepted" class="btn accept-btn">Accept</button>
                                        <button name="status" value="Rejected" class="btn reject-btn">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h3>Accepted Patients</h3>

        <?php if (empty($acceptedRequests)): ?>
            <p class="empty">No accepted patients yet.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="caregiver-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($acceptedRequests as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= htmlspecialchars($req['phone_number'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.table-container {
    margin-top: 20px;
    overflow-x: auto;
}

.caregiver-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(41, 157, 151, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.caregiver-table th, .caregiver-table td {
    padding: 14px 18px;
    text-align: center;
    border-bottom: 1px solid #e0f2f1;
}

.caregiver-table th {
    background-color: #e4f9f8;
    color: #299D97;
    font-weight: 600;
}

.caregiver-table td {
    font-size: 14px;
    color: #333;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.accept-btn {
    background-color: #28a745;
}

.reject-btn {
    background-color: #dc3545;
}

.accept-btn:hover {
    background-color: #218838;
}

.reject-btn:hover {
    background-color: #c82333;
}
</style>
