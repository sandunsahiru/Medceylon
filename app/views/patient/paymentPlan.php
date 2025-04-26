<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patient/paymentPlan.css">

<div class="main-container">
    <section class="plan-containers">
        <h2>Our Payment Plans</h2>
        <div class="plan-containers-row">

            <?php if (!empty($paymentPlans)): ?>
                <?php foreach ($paymentPlans as $plan): ?>
                    <div class="plan-container" 
                         style="border: 2px solid; border-image: linear-gradient(to bottom, #70706F, #BEC0C2, #8E8D8D, #8E8D8D) 1;">
                        <p>
                            <ul>
                                <?php foreach (json_decode($plan['benefits']) as $benefit): ?>
                                    <li><?php echo htmlspecialchars($benefit); ?></li>
                                <?php endforeach; ?>
                                <li><b>$<?php echo htmlspecialchars($plan['price']); ?></b></li>
                            </ul>
                        </p>
                        <p class="name"><?php echo htmlspecialchars($plan['plan_name']); ?></p>
                        <button onclick="assignPlan(<?= htmlspecialchars($plan['id']) ?>)">Choose Plan</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No payment plans available at the moment.</p>
            <?php endif; ?>

        </div>
    </section>
</div>


<script>
    function assignPlan(planId) {
        if (!confirm('Are you sure you want to select this plan?')) {
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo $basePath; ?>/patient/choose-plan', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Payment plan assigned successfully!');
                    window.location.href = '<?php echo $basePath; ?>/home';
                } else {
                    alert('Failed to assign plan: ' + response.message);
                }
            } else {
                alert('Server error!');
            }
        };

        xhr.send('plan_id=' + planId);
    }
</script>
