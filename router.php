<?php
include_once 'controllers/UserController.php';
include_once 'controllers/TransactionController.php';
require __DIR__ . '/vendor/autoload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

$action = $_GET['action'] ?? 'start';

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            $confirmPassword = htmlspecialchars($_POST['confirm-password']);
            $email = htmlspecialchars($_POST['email']);
            $totpCode = htmlspecialchars($_POST['totp']);

            // Walidacja nazwy użytkownika
            if (!preg_match('/^[a-zA-Z0-9]+$/', $username) || strlen($username) < 8) {
                header("Location: router.php?action=register&error=invalid_username");
                exit();
            }

            // Sprawdzenie zgodności haseł
            if ($password !== $confirmPassword) {
                header("Location: router.php?action=register&error=password_mismatch");
                exit();
            }

            // Walidacja hasła
            $emailUsername = explode('@', $email)[0];
            $passwordCriteria = [
                '/.{12,}/', // Minimum 12 znaków
                '/[a-z]/',  // Małe litery
                '/[A-Z]/',  // Wielkie litery
                '/[0-9]/',  // Cyfry
                '/[!@#$%^&*(),.?":{}|<>]/', // Znaki specjalne
                '/^(?!.*' . preg_quote($username, '/') . ')/', // Bez nazwy użytkownika
                '/^(?!.*' . preg_quote($emailUsername, '/') . ')/' // Bez pierwszej części maila
            ];

            foreach ($passwordCriteria as $regex) {
                if (!preg_match($regex, $password)) {
                    header("Location: router.php?action=register&error=invalid_password");
                    exit();
                }
            }

            // Sprawdzenie unikalności e-maila
            if ($userController->emailExists($email)) {
                header("Location: router.php?action=register&error=email_taken");
                exit();
            }

            // Rejestracja użytkownika
            if ($userController->register($username, $password, $email, $totpCode)) {
                header("Location: router.php?action=login");
            } else {
                header("Location: router.php?action=register&error=invalid_totp");
            }
        } else {
            include 'views/register.php';
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            $totpCode = htmlspecialchars($_POST['totp']);
            $user = $userController->login($username, $password, $totpCode);
            if ($user) {
                // Weryfikacja TOTP code
                $totp = \OTPHP\TOTP::create($user['totp_secret']);
                if ($totp->verify($totpCode)) {
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: router.php?action=dashboard");
                } else {
                    echo "Invalid TOTP code.";
                }
            } else {
                echo "Login failed.";
            }
        } else {
            include 'views/login.php';
        }
        break;

    case 'dashboard':
        include 'views/dashboard.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: router.php?action=login");
        break;

    case 'start':
    default:
        include 'views/start.php';
        break;
}
?>
