<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Bank</title>
    <link rel="stylesheet" href="/bank_system/css/styles.css">
</head>
<body>
<main>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="router.php?action=login">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="totp">TOTP Code</label>
                <input type="text" id="totp" name="totp" placeholder="Enter TOTP code from Google Authenticator" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</main>
</body>
</html>
