<?php

require_once 'includes/db.php';
require __DIR__ . '/../vendor/autoload.php';

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function register($username, $password, $email, $totpSecret)
    {
        if ($this->emailExists($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $accountNumber = $this->generateAccountNumber($hashedPassword);
        $role = 'user'; // Domyślna wartość roli

        $sql = "INSERT INTO users (username, password, email, role, account_number, totp_secret) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $hashedPassword, $email, $role, $accountNumber, $totpSecret);
        return $stmt->execute();
    }

    public function login($username, $password)
    {
        $sql = "SELECT id, password, totp_secret FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return ['id' => $row['id'], 'totp_secret' => $row['totp_secret']];
            }
        }
        
        return false;
    }

    public function getUserById($id)
    {
        $sql = "SELECT username, role, account_number FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function emailExists($email)
    {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    private function generateAccountNumber($hashedPassword)
    {
        // Generowanie numeru konta na podstawie hasha hasła
        $hash = hash('sha256', $hashedPassword);
        $numericHash = preg_replace('/[^0-9]/', '', $hash);
        $accountNumber = substr($numericHash, 0, 26);

        // Formatowanie numeru konta: BB BBBB BBBB BBBB BBBB BBBB BBBB
        $formattedAccountNumber = 'PL' . substr($accountNumber, 0, 2) . ' ' .
            substr($accountNumber, 2, 4) . ' ' .
            substr($accountNumber, 6, 4) . ' ' .
            substr($accountNumber, 10, 4) . ' ' .
            substr($accountNumber, 14, 4) . ' ' .
            substr($accountNumber, 18, 4) . ' ' .
            substr($accountNumber, 22, 4);

        return $formattedAccountNumber;
    }
}
