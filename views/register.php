<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../vendor/autoload.php';

use OTPHP\TOTP;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;

// Generowanie TOTP
$totp = TOTP::create();
$totp->setLabel('YourAppName');

// Przechowaj TOTP secret w sesji tymczasowo dla procesu rejestracji
$_SESSION['totp_secret'] = $totp->getSecret();

// Generowanie QR kodu
$qrCode = QrCode::create($totp->getProvisioningUri());
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Ścieżka do zapisu QR kodu
$qrCodePath = __DIR__ . '/tmp/qrcode.png';

// Upewnij się, że katalog tmp istnieje
if (!file_exists(__DIR__ . '/tmp')) {
    mkdir(__DIR__ . '/tmp', 0777, true);
}

// Zapisz QR kod jako obrazek w katalogu tymczasowym
$result->saveToFile($qrCodePath);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - Bank</title>
    <link rel="stylesheet" href="/bank_system/css/styles.css">
    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var email = document.getElementById("email").value;
            var emailUsername = email.split('@')[0];

            if (!/^[a-zA-Z0-9]+$/.test(username)) {
                showAlert("Invalid username. It can contain only letters and numbers.");
                return false;
            }

            if (username.length < 8) {
                showAlert("Username must be at least 8 characters long.");
                return false;
            }

            if (password !== confirmPassword) {
                showAlert("Passwords do not match.");
                return false;
            }

            var passwordCriteria = [
                { regex: /.{12,}/, message: "Password must be at least 12 characters long." },
                { regex: /[a-z]/, message: "Password must contain at least one lowercase letter." },
                { regex: /[A-Z]/, message: "Password must contain at least one uppercase letter." },
                { regex: /[0-9]/, message: "Password must contain at least one digit." },
                { regex: /[!@#$%^&*(),.?":{}|<>]/, message: "Password must contain at least one special character." },
                { regex: new RegExp("^(?!.*" + username + ").*"), message: "Password must not contain the username." },
                { regex: new RegExp("^(?!.*" + emailUsername + ").*"), message: "Password must not contain the first part of the email." }
            ];

            for (var i = 0; i < passwordCriteria.length; i++) {
                if (!passwordCriteria[i].regex.test(password)) {
                    showAlert(passwordCriteria[i].message);
                    return false;
                }
            }

            return true;
        }

        function showAlert(message) {
            var alertBox = document.createElement("div");
            alertBox.className = "alert-box";
            alertBox.innerText = message;

            var closeBtn = document.createElement("span");
            closeBtn.className = "close-btn";
            closeBtn.innerText = "x";
            closeBtn.onclick = function() {
                alertBox.style.display = "none";
            };

            alertBox.appendChild(closeBtn);
            document.body.appendChild(alertBox);

            setTimeout(function() {
                alertBox.style.display = "none";
            }, 3000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                var errorMessage = "";
                switch (urlParams.get('error')) {
                    case 'invalid_username':
                        errorMessage = "Invalid username. It can contain only letters and numbers and must be at least 8 characters long.";
                        break;
                    case 'invalid_password':
                        errorMessage = "Invalid password. Please ensure it meets all criteria.";
                        break;
                    case 'password_mismatch':
                        errorMessage = "Passwords do not match.";
                        break;
                    case 'email_taken':
                        errorMessage = "This email is already taken. Please use a different email.";
                        break;
                    case 'invalid_totp':
                        errorMessage = "Invalid TOTP code. Please try again.";
                        break;
                    default:
                        errorMessage = "An unknown error occurred.";
                        break;
                }
                showAlert(errorMessage);
            }
        });
    </script>
    <style>
        .alert-box {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f44336;
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .close-btn {
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<main>
    <div class="register-container">
        <h2>Register</h2>
        <form method="post" action="router.php?action=register" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required pattern="[a-zA-Z0-9]+" minlength="8" title="Username can contain only letters and numbers and must be at least 8 characters long">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="totp">TOTP Code</label>
                <input type="text" id="totp" name="totp" placeholder="Enter TOTP code from Google Authenticator" required>
                <img src="views/tmp/qrcode.png" alt="QR Code">
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</main>
</body>
</html>
