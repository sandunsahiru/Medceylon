<h2>Caregiver Requests</h2>
<?php while ($req = $requests->fetch_assoc()): ?>
    <div class="card">
        <p><strong><?= $req['first_name'] . ' ' . $req['last_name'] ?></strong> says:</p>
        <p><?= $req['message'] ?></p>
        <p>Status: <?= $req['status'] ?></p>
        <?php if ($req['status'] === 'Pending'): ?>
            <form action="<?= $basePath ?>/caregiver/respond/<?= $req['request_id'] ?>" method="POST">
                <button name="status" value="Accepted">Accept</button>
                <button name="status" value="Rejected">Reject</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
