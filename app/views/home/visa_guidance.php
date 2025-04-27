<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sri Lanka Visa Information</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9f8;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header,
        footer {
            width: 100%;
            flex-shrink: 0;
            background-color: #299d97;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .container {
            flex-grow: 1;
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #299d97;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            color: #299d97;
        }

        select {
            padding: 10px;
            font-size: 1rem;
            border: 2px solid #299d97;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #299d97;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #247f7a;
        }

        .details {
            margin-top: 20px;
        }

        .details h2 {
            color: #299d97;
            margin-bottom: 10px;
        }

        a {
            color: #299d97;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        pre {
            background: #f1f9f9;
            border-left: 4px solid #299d97;
            padding: 10px;
            border-radius: 5px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

    <div class="container">
        <h1>Sri Lanka Visa Information</h1>
        <form method="POST">
            <label for="country_code">Select Country:</label>
            <select name="country_code" id="country_code" required>
                <option value="">-- Choose a Country --</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?= htmlspecialchars($country['country_code']); ?>">
                        <?= htmlspecialchars($country['country_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Show Visa Details</button>
        </form>

        <?php if ($country_details): ?>
            <div class="details">
                <h2>Visa Details for <?= htmlspecialchars($country_details['country_name']); ?></h2>
                <?php if ($country_details['visa_required']): ?>
                    <p><strong>Visa Required:</strong> Yes</p>
                    <p><strong>Steps to Apply for Visa:</strong></p>
                    <pre><?= htmlspecialchars($country_details['application_steps']); ?></pre>
                <?php else: ?>
                    <p><strong>No visa is required for citizens of
                            <?= htmlspecialchars($country_details['country_name']); ?>.</strong></p>
                <?php endif; ?>
                <p><strong>Embassy Link:</strong> <a href="<?= htmlspecialchars($country_details['embassy_link']); ?>"
                        target="_blank"><?= htmlspecialchars($country_details['embassy_link']); ?></a></p>
            </div>
        <?php endif; ?>
    </div>

    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
</body>

</html>