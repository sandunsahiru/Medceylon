<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="error-container">
    <div class="error-content">
        <div class="error-icon">
            <i class="ri-error-warning-line"></i>
        </div>
        <h1>Error</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="<?php echo $basePath; ?>/doctor/dashboard" class="back-btn">
            <i class="ri-arrow-left-line"></i>
            Back to Dashboard
        </a>
    </div>
</div>

<style>
.error-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: #f8f9fa;
}

.error-content {
    background: white;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
}

.error-icon {
    font-size: 48px;
    color: #dc3545;
    margin-bottom: 20px;
}

.error-content h1 {
    color: #343a40;
    margin-bottom: 15px;
}

.error-content p {
    color: #6c757d;
    margin-bottom: 25px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.back-btn:hover {
    background: #0056b3;
}
</style>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>