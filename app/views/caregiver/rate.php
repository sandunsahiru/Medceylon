<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>
<link rel="stylesheet" href="/Medceylon/public/assets/css/caregiver.css?v=4">

<div class="page-wrapper">
    <div class="container">
        <h2 class="page-title">Rate Your Caregiver</h2>

        <form method="POST" action="">
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="review">Review (optional):</label>
                <textarea name="review" rows="4" placeholder="Write something..."></textarea>
            </div>

            <button type="submit" class="btn">Submit Rating</button>
        </form>
    </div>
</div>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
