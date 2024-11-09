<?php
session_start();

// Als de gebruiker niet is ingelogd, doorverwijzen naar de inlogpagina
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Haal de gebruikersgegevens op uit de sessie
$user = $_SESSION['user'];
$role = $user['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OSHosting</title>
    <link rel="stylesheet" href="static/styles.css">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
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

        <!-- Content in het dashboard -->
        <div class="content">
            <h2>Welkom, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            
            <!-- Dashboard-inhoud voor een admin -->
            <?php if ($role == 'admin'): ?>
                <p>Je bent ingelogd als admin. Hieronder staan de beheermogelijkheden:</p>
                <ul>
                    <li>Nieuwe gebruikers toevoegen</li>
                    <li>Nieuwe instances beheren</li>
                    <li>Accounts beheren</li>
                </ul>

            <!-- Dashboard-inhoud voor een gewone gebruiker -->
            <?php else: ?>
                <p>Je bent ingelogd als gebruiker. Hieronder staan je opties:</p>
                <ul>
                    <li>Bekijk en beheer je instances</li>
                    <li>Beheer je accountinstellingen</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>