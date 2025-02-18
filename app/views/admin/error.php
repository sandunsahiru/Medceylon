<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }
        h1 {
            color: #dc3545;
            font-size: 2rem;
        }
        p {
            font-size: 1rem;
        }
        .stack-trace {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-family: monospace;
            overflow-x: auto;
            max-height: 300px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error Occurred</h1>
        <p><strong>Message:</strong> <?= isset($message) ? htmlspecialchars($message) : 'An error occurred, but no specific message was provided.'; ?></p>
        
        <?php if (isset($file)): ?>
            <p><strong>File:</strong> <?= htmlspecialchars($file); ?></p>
        <?php endif; ?>

        <?php if (isset($line)): ?>
            <p><strong>Line:</strong> <?= htmlspecialchars($line); ?></p>
        <?php endif; ?>

        <?php if (isset($trace)): ?>
            <h2>Stack Trace:</h2>
            <div class="stack-trace">
                <?= nl2br(htmlspecialchars($trace)); ?>
            </div>
        <?php endif; ?>

        <a href="<?php echo $basePath; ?>/admin/dashboard" class="back-link">Go Back to Dashboard</a>
    </div>
</body>
</html>
