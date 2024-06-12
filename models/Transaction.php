<?php
include_once 'includes/db.php';

class Transaction {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getTransactionsByUserId($user_id) {
        $sql = "SELECT amount, transaction_date, description FROM transactions WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
