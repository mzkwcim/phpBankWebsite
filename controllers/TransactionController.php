<?php
include_once 'models/Transaction.php';

class TransactionController {
    private $transaction;

    public function __construct() {
        $this->transaction = new Transaction();
    }

    public function getTransactionsByUserId($user_id) {
        return $this->transaction->getTransactionsByUserId($user_id);
    }
}
?>
