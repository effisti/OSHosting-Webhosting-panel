<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Functie om accounts op te slaan naar accounts.json
function saveAccounts($accounts) {
    file_put_contents('includes/accounts.json', json_encode($accounts, JSON_PRETTY_PRINT));
}

// Functie om accounts te laden vanuit accounts.json
function loadAccounts() {
    $json = file_get_contents('includes/accounts.json');
    return json_decode($json, true);
}

// Functie om nieuwe account aan te maken
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Wachtwoord wordt gehashed
    $role = $_POST['role'];

    // Laad huidige accounts
    $accounts = loadAccounts();

    // Check of gebruikersnaam al bestaat
    if (isset($accounts[$username])) {
        $error = "Gebruiker bestaat al.";
    } else {
        // Voeg nieuwe gebruiker toe
        $accounts[$username] = [
            'username' => $username,
            'password' => $password,
            'role' => $role
        ];

        // Sla accounts op in het JSON-bestand
        saveAccounts($accounts);
        $success = "Account succesvol aangemaakt.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Account Toevoegen</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
            <div class="dashboard-container">
        <!-- Sidebar met opties -->
        <div class="sidebar">
            <ul>
                <li><a href="dashboard">Dashboard</a></li>
                <li><a href="account">Account</a></li>
                <li><a href="instances.php">Instances</a></li>

                <!-- Alleen zichtbaar voor admins -->
                <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin'): ?>
                    <li><a href="overzicht-accounts">Accountsoverzicht</a></li>
                    <li><a href="add-account">Account Toevoegen</a></li>
                    <li><a href="instance-toevoegen">Instance Toevoegen</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="content">
            <h2>Nieuw Account Toevoegen</h2>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success-message'>$success</p>"; ?>
            <form method="POST" action="add_account.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn btn-primary">Account Aanmaken</button>
            </form>
        </div>
    </div>
</body>
</html>