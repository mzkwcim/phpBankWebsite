<?php

require_once 'models/User.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload Composer dependencies

use OTPHP\TOTP;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register($username, $password, $email, $totpCode)
    {
        // Pobieranie TOTP secret z sesji
        $totpSecret = $_SESSION['totp_secret'];

        // Weryfikacja TOTP code
        $totp = TOTP::create($totpSecret);
        if (!$totp->verify($totpCode)) {
            return false;
        }

        // Rejestracja użytkownika
        return $this->userModel->register($username, $password, $email, $totpSecret);
    }

    public function emailExists($email)
    {
        return $this->userModel->emailExists($email);
    }

    public function login($username, $password, $totpCode)
    {
        $user = $this->userModel->login($username, $password);
        if ($user) {
            $totp = TOTP::create($user['totp_secret']);
            if ($totp->verify($totpCode)) {
                $_SESSION['user_id'] = $user['id'];
                return true;
            }
        }
        return false;
    }
}
