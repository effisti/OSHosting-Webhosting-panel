<?php
session_start();

// Functie om accounts te laden vanuit accounts.json
function loadAccounts() {
    $json = file_get_contents('includes/accounts.json');
    return json_decode($json, true);
}

// Functie om accounts op te slaan in accounts.json
function saveAccounts($accounts) {
    file_put_contents('includes/accounts.json', json_encode($accounts, JSON_PRETTY_PRINT));
}

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['user']['username'];
$accounts = loadAccounts();
$user = $accounts[$username];

// Initialiseer foutmeldingen
$error = '';
$success = '';

// Verwerken van de formulieren
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newUsername = trim($_POST['username']);
    $newPassword = trim($_POST['password']); // Trim whitespace

    // Validatie
    if ($newUsername === '') {
        $error = 'Gebruikersnaam mag niet leeg zijn.';
    } elseif ($newPassword === '') {
        $error = 'Wachtwoord mag niet leeg zijn.';
    } else {
        // Update gebruikersgegevens
        if ($newUsername !== $username) {
            // Controleer of de nieuwe gebruikersnaam al bestaat
            if (isset($accounts[$newUsername])) {
                $error = 'Deze gebruikersnaam is al in gebruik.';
            } else {
                unset($accounts[$username]); // Verwijder de oude gebruikersnaam
                $user['username'] = $newUsername; // Update gebruikersnaam
                $accounts[$newUsername] = $user; // Voeg de gebruiker opnieuw toe met de nieuwe gebruikersnaam
                $username = $newUsername; // Update de huidige sessie
            }
        }

        // Update wachtwoord
        $user['password'] = $newPassword; // Gebruik het nieuwe wachtwoord zoals het is
        $accounts[$username] = $user; // Zorg ervoor dat de gebruiker met de nieuwe gegevens is opgeslagen

        // Sla de accounts op
        saveAccounts($accounts);

        // Update sessie
        $_SESSION['user']['username'] = $username;

        $success = 'Accountgegevens zijn succesvol bijgewerkt.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account | OSHosting</title>
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
            <h2>Account Gegevens</h2>

            <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
            <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>

            <form method="POST" action="account.php">
                <label for="username">Nieuwe Gebruikersnaam:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

                <label for="password">Nieuw Wachtwoord:</label>
                <input type="text" id="password" name="password" required>

                <button type="submit">Bijwerken</button>
            </form>
        </div>
    </div>
</body>
</html>