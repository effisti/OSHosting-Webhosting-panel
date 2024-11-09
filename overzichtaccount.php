<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Functie om accounts te laden vanuit accounts.json
function loadAccounts() {
    $json = file_get_contents('includes/accounts.json');
    return json_decode($json, true);
}

// Functie om accounts te verwijderen
if (isset($_GET['delete'])) {
    $usernameToDelete = $_GET['delete'];

    // Laad huidige accounts
    $accounts = loadAccounts();

    // Verwijder de account als deze bestaat
    if (isset($accounts[$usernameToDelete])) {
        unset($accounts[$usernameToDelete]);
        file_put_contents('includes/accounts.json', json_encode($accounts, JSON_PRETTY_PRINT));
        $success = "Account '{$usernameToDelete}' is succesvol verwijderd.";
    } else {
        $error = "Account '{$usernameToDelete}' bestaat niet.";
    }
}

// Laad accounts
$accounts = loadAccounts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Account Overzicht</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="stylesheet" href="static/styles.css">
    <style>
        /* Stijlen voor de pagina */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            display: flex;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .delete-button {
            padding: 5px 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        .success-message, .error-message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success-message {
            color: green;
            background-color: #d4edda;
        }

        .error-message {
            color: red;
            background-color: #f8d7da;
        }
    </style>
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
            <h2>Account Overzicht</h2>

            <?php if (isset($success)) echo "<div class='success-message'>$success</div>"; ?>
            <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $username => $account): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><?php echo htmlspecialchars($account['role']); ?></td>
                            <td>
                                <a href="accountoverzicht.php?delete=<?php echo urlencode($username); ?>" onclick="return confirm('Weet je zeker dat je dit account wilt verwijderen?');" class="delete-button">Verwijder</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>