<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Functie om instances te laden vanuit instances.json
function loadInstances() {
    $json = file_get_contents('includes/instances.json');
    return json_decode($json, true);
}

// Laad de instances
$instances = loadInstances();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instances | OSHosting</title>
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

        <div class="content">
     <h2>Instances</h2>
     <ul>
          <?php foreach ($instances as $instance): ?>
               <?php if ($instance['owner'] == $user['username'] || $role == 'admin'): ?>
                   <li>
                       <a href="https://oshosting.totalh.net/instances/<?php echo urlencode($instance['name']); ?>/index" class="btn">
                          <?php echo htmlspecialchars($instance['name']); ?>
                      </a> - Status: Started
                      <a href="instanceinfo.php?name=<?php echo urlencode($instance['name']); ?>">Bewerk</a>
                  </li>
               <?php endif; ?>
            <?php endforeach; ?>
     </ul>
    </div>
    </div>
</body>
</html>