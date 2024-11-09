<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Functie om instances op te slaan naar instances.json
function saveInstances($instances) {
    file_put_contents('includes/instances.json', json_encode($instances, JSON_PRETTY_PRINT));
}

// Functie om instances te laden
function loadInstances() {
    $json = file_get_contents('includes/instances.json');
    return json_decode($json, true);
}

// Functie om accounts te laden vanuit accounts.json
function loadAccounts() {
    $json = file_get_contents('includes/accounts.json');
    return json_decode($json, true);
}

// Laad de accounts
$accounts = loadAccounts();
$usernames = array_keys($accounts); // Haal de gebruikersnamen op

// Functie om een nieuwe instance toe te voegen
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $instanceName = $_POST['instance_name'];
    $owner = $_POST['owner'];

    // Laad huidige instances
    $instances = loadInstances();

    // Voeg nieuwe instance toe
    $instances[] = [
        'name' => $instanceName,
        'owner' => $owner, // Opslaan als een string voor de eigenaar
        'status' => 'stopped',
        'creation_date' => date('Y-m-d')
    ];

    // Sla de instances op in het JSON-bestand
    saveInstances($instances);
    
    // Maak een map voor de nieuwe instance aan
    $instanceFolder = "instances/{$instanceName}/";
    if (!file_exists($instanceFolder)) {
        mkdir($instanceFolder, 0777, true);
    }

    $success = "Instance succesvol aangemaakt.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Instance Toevoegen</title>
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
            <h2>Nieuwe Instance Toevoegen</h2>
            <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
            <form method="POST" action="add_instance.php">
                <label for="instance_name">Instance Naam:</label>
                <input type="text" id="instance_name" name="instance_name" required>

                <label for="owner">Eigenaar:</label>
                <select id="owner" name="owner" required>
                    <option value="">Selecteer een eigenaar</option>
                    <?php foreach ($usernames as $username): ?>
                        <option value="<?php echo $username; ?>"><?php echo $username; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">Instance Aanmaken</button>
            </form>
        </div>
    </div>
</body>
</html>
