<?php
// This is a one-time script to generate a secure password hash
// Access it at https://your-domain.com/utils/generate_hash.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    echo "Generated hash: " . $hash . "\n";
    echo "Copy this hash into your login.php file\n";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Password Hash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        form { max-width: 400px; margin: 30px auto; }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">Generate Password Hash</h2>
            </div>
            <div class="card-body">
                <p>Enter your desired password to generate a hash that can be used in the login system:</p>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn btn-primary">Generate Hash</button>
                <div class="mt-3 small text-muted">
                    The generated hash should be placed in the $stored_hash variable in login.php.
                </div>
            </div>
        </form>
    </div>
</body>
</html> 