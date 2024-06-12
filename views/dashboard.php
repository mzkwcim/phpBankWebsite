<?php include 'header.php'; ?>

<h2>Dashboard z Transakcjami</h2>
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Kwota</th>
            <th>Odbiorca</th>
        </tr>
    </thead>
    <tbody id="transaction-list">
        <!-- Transakcje będą tutaj dodawane przez JavaScript -->
    </tbody>
</table>
<button onclick="addNewTransaction()">Dodaj Nową Transakcję</button>
<div id="filters">
    <label for="filter-date">Data:</label>
    <input type="date" id="filter-date" name="filter-date">
    <label for="filter-amount">Kwota:</label>
    <input type="number" id="filter-amount" name="filter-amount">
    <label for="filter-type">Rodzaj:</label>
    <select id="filter-type" name="filter-type">
        <option value="all">Wszystkie</option>
        <option value="income">Wpływy</option>
        <option value="expense">Wydatki</option>
    </select>
</div>

<?php include 'footer.php'; ?>
