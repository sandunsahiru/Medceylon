<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Caregiver Dashboard</title>
  <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/caregiver.css">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f6f8;
      overflow: hidden;
    }

    .dashboard {
      display: flex;
      width: 100vw;
      height: 100vh;
    }

    .sidebar {
      width: 280px;
      background-color: #ffffff;
      padding: 30px 20px;
      border-right: 1px solid #e0e0e0;
      box-sizing: border-box;
    }

    .sidebar h2 {
      font-size: 20px;
      color: #00695c;
      margin-bottom: 30px;
    }

    .sidebar a {
      display: block;
      color: #333;
      text-decoration: none;
      padding: 12px 18px;
      border-radius: 6px;
      margin-bottom: 10px;
      font-weight: 500;
      transition: background-color 0.2s;
    }

    .sidebar a:hover {
      background-color: #f0f0f0;
    }

    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .content {
      padding: 30px 40px;
      overflow-y: auto;
      height: 100vh;
      box-sizing: border-box;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .card {
      background-color: #ffffff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
      border: 1px solid #eaeaea;
    }

    .card h3 {
      color: #00796b;
      font-size: 16px;
      margin-bottom: 8px;
    }

    .card span {
      font-size: 28px;
      font-weight: bold;
    }

    .section {
      margin-top: 30px;
    }

    .section h2 {
      font-size: 20px;
      color: #004d40;
      margin-bottom: 10px;
      border-bottom: 1px solid #ccc;
      padding-bottom: 6px;
    }

    .caregiver-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }

    .caregiver-card {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      border: 1px solid #ddd;
    }

    .caregiver-card h3 {
      margin-bottom: 10px;
    }

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
    }

    .btn.primary {
      background-color: #26a69a;
      color: white;
    }

    .btn.secondary {
      background-color: #ef5350;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Caregiver Dashboard</h2>
    <a href="<?= $basePath ?>/caregiver/dashboard">Dashboard</a>
    <a href="<?= $basePath ?>/logout">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="content">
      <div class="stats">
        <div class="card">
          <h3>New Messages</h3>
          <span><?= $messages->num_rows ?? 0 ?></span>
        </div>
        <div class="card">
          <h3>Pending Requests</h3>
          <span>
            <?php
            $pendingCount = 0;
            if ($requests->num_rows > 0) {
              foreach ($requests as $req) {
                if ($req['status'] === 'Pending') $pendingCount++;
              }
            }
            echo $pendingCount;
            ?>
          </span>
        </div>
      </div>

      <!-- Messages -->
      <div class="section">
        <h2>Your Patient Messages</h2>
        <?php if ($messages->num_rows > 0): ?>
          <div class="caregiver-grid">
            <?php while ($msg = $messages->fetch_assoc()): ?>
              <div class="caregiver-card">
                <h3><?= $msg['first_name'] . ' ' . $msg['last_name'] ?></h3>
                <a href="<?= $basePath ?>/caregiver/chat/<?= $msg['sender_id'] ?>" class="btn primary">Open Chat</a>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p>No messages yet.</p>
        <?php endif; ?>
      </div>

      <!-- Requests -->
      <div class="section">
        <h2>Caregiving Requests</h2>
        <?php if ($requests->num_rows > 0): ?>
          <div class="caregiver-grid">
            <?php while ($req = $requests->fetch_assoc()): ?>
              <div class="caregiver-card">
                <h3><?= $req['first_name'] . ' ' . $req['last_name'] ?></h3>
                <p><strong>Message:</strong> <?= $req['message'] ?></p>
                <p><strong>Status:</strong> <?= $req['status'] ?></p>
                <?php if ($req['status'] === 'Pending'): ?>
                  <form method="POST" action="<?= $basePath ?>/caregiver/respond/<?= $req['request_id'] ?>">
                    <button name="status" value="Accepted" class="btn primary">Accept</button>
                    <button name="status" value="Rejected" class="btn secondary">Reject</button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p>No caregiving requests yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
