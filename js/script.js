
document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            event.preventDefault();

            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('confirm-password').value;

            if (password !== confirmPassword) {
                alert('Hasła nie są takie same!');
                return;
            }

            alert('Rejestracja udana!');
        });
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;

            if (email === "user@example.com" && password === "password") {
                alert('Logowanie udane!');
                window.location.href = "powitanie.html";
            } else {
                alert('Nieprawidłowy email lub hasło!');
            }
        });
    }

    const transactionButton = document.querySelector('button[onclick="addNewTransaction()"]');
    if (transactionButton) {
        transactionButton.addEventListener('click', addNewTransaction);
    }

    const filterDate = document.getElementById('filter-date');
    if (filterDate) {
        filterDate.addEventListener('input', function(event) {
            let filterDate = event.target.value;
            let transactions = document.querySelectorAll('#transaction-list tr');
            
            transactions.forEach(transaction => {
                let transactionDate = transaction.children[0].textContent;
                if (transactionDate !== filterDate) {
                    transaction.style.display = 'none';
                } else {
                    transaction.style.display = '';
                }
            });
        });
    }

    function addNewTransaction() {
        alert('Dodaj nową transakcję.');
    }

    const transferForm = document.getElementById('transfer-form');
    if (transferForm) {
        transferForm.addEventListener('submit', function(event) {
            event.preventDefault();

            let recipient = document.getElementById('recipient').value;
            let accountNumber = document.getElementById('account-number').value;
            let amount = document.getElementById('amount').value;
            let title = document.getElementById('title').value;
            let saveTemplate = document.getElementById('save-template').checked;


            if (saveTemplate) {
                alert('Przelew został zapisany jako szablon.');
            }

            alert('Przelew wysłany pomyślnie!');
        });
    }
});
