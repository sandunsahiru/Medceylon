<!-- views/error.php -->
<?php
$pageTitle = 'Error';
include 'templates/main_header.php';
include 'templates/navbar.php';
?>

<div class="content">
    <h2>Error</h2>
    <p><?php echo isset($errorMessage) ? htmlspecialchars($errorMessage) : 'An unexpected error occurred.'; ?></p>
</div>

<?php include 'templates/main_footer.php'; ?>
