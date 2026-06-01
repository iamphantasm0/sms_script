<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SESSION['authed'] ?? false) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === DASHBOARD_USER && password_verify($pass, DASHBOARD_PASS)) {
        session_regenerate_id(true);
        $_SESSION['authed'] = true;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Dashboard — Login</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 380px;
        }

        .login-card h1 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #f1f5f9;
        }

        .login-card p.subtitle {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 2rem;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.65rem 0.85rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
            color: #f1f5f9;
            font-size: 0.95rem;
            margin-bottom: 1.1rem;
            outline: none;
            transition: border-color 0.15s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3b82f6;
        }

        .error-msg {
            background: #450a0a;
            border: 1px solid #991b1b;
            color: #fca5a5;
            padding: 0.6rem 0.85rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 1.1rem;
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.7rem;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        button[type="submit"]:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>SMS Dashboard</h1>
        <p class="subtitle">Internal notification script runner</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
