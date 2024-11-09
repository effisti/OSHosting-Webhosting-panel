<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Functie om instances te laden vanuit instances.json
function loadInstances() {
    $json = file_get_contents('includes/instances.json');
    return json_decode($json, true);
}

// Haal de naam van de instance op
$instanceName = $_GET['name'] ?? '';
$instances = loadInstances();
$instance = null;

// Zoek de juiste instance
foreach ($instances as $inst) {
    if ($inst['name'] == $instanceName) {
        $instance = $inst;
        break;
    }
}

// Als de instance niet bestaat, doorverwijzen naar instances.php
if (!$instance) {
    header('Location: instances.php');
    exit;
}

// Functie om bestanden in de instance-directory te laden
function loadFiles($instanceName, $subDir = '') {
    $files = [];
    $path = "instances/{$instanceName}/" . $subDir;

    if (is_dir($path)) {
        $files = array_diff(scandir($path), ['.', '..']);
    }

    return $files;
}

// Haal de subdirectory op
$subDir = $_GET['subdir'] ?? '';
$files = loadFiles($instanceName, $subDir);

// Maak nieuwe map aan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newFolderName'])) {
    $newFolderName = trim($_POST['newFolderName']);
    $newFolderPath = "instances/{$instanceName}/{$subDir}" . $newFolderName;

    if (!file_exists($newFolderPath)) {
        mkdir($newFolderPath, 0777, true); // Maak de nieuwe map aan
        $newFolderMessage = "Map '$newFolderName' is aangemaakt.";
        $files = loadFiles($instanceName, $subDir); // Vernieuw de lijst met bestanden en mappen
    } else {
        $newFolderMessage = "Map bestaat al.";
    }
}

// Functie om breadcrumbs te genereren voor de huidige locatie
function generateBreadcrumbs($instanceName, $subDir) {
    $breadcrumb = '<a href="instanceinfo.php?name=' . urlencode($instanceName) . '">root</a>'; // Start met root
    $dirs = explode('/', trim($subDir, '/'));

    $currentPath = '';
    foreach ($dirs as $dir) {
        if ($dir) {
            $currentPath .= $dir . '/';
            $breadcrumb .= ' <i class="fas fa-chevron-right"></i> '; // Voeg het chevron-icoon toe
            $breadcrumb .= '<a href="instanceinfo.php?name=' . urlencode($instanceName) . '&subdir=' . urlencode($currentPath) . '">' . htmlspecialchars($dir) . '</a>';
        }
    }

    return $breadcrumb;
}

// Bestanden uploaden
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $targetDir = "instances/{$instanceName}/{$subDir}";
    $targetFile = $targetDir . basename($_FILES['fileToUpload']['name']);
    
    // Controleer of het bestand al bestaat
    if (file_exists($targetFile)) {
        $uploadMessage = "Sorry, bestand bestaat al.";
    } else {
        // Probeer het bestand te uploaden
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
            $uploadMessage = "Bestand " . htmlspecialchars(basename($_FILES['fileToUpload']['name'])) . " is geüpload.";
            $files = loadFiles($instanceName, $subDir); // Vernieuw de lijst met bestanden
        } else {
            $uploadMessage = "Sorry, er was een fout bij het uploaden van je bestand.";
        }
    }
}

// Verwijder bestand
if (isset($_GET['delete'])) {
    $fileToDelete = $_GET['delete'];
    $filePath = "instances/{$instanceName}/" . $subDir . $fileToDelete;

    if (file_exists($filePath)) {
        unlink($filePath);
        header("Location: instanceinfo.php?name=" . urlencode($instanceName) . "&subdir=" . urlencode($subDir));
        exit;
    } else {
        $deleteMessage = "Bestand niet gevonden.";
    }
}

// Maak nieuw bestand aan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newFileName'])) {
    $newFileName = trim($_POST['newFileName']);
    $newFilePath = "instances/{$instanceName}/{$subDir}" . $newFileName;

    if (!file_exists($newFilePath)) {
        file_put_contents($newFilePath, ""); // Maak een leeg bestand aan
        $newFileMessage = "Bestand '$newFileName' is aangemaakt.";
        $files = loadFiles($instanceName, $subDir); // Vernieuw de lijst met bestanden
    } else {
        $newFileMessage = "Bestand bestaat al.";
    }
}

// Aantal bestanden in de map
$fileCount = is_array($files) ? count($files) : 0;

// Hoe lang bestaat de instance al (gebaseerd op creation_date)
$creationDate = $instance['creation_date'] ?? ''; // Zorg ervoor dat creation_date beschikbaar is
if ($creationDate) {
    $creationDateTime = new DateTime($creationDate);
    $currentDate = new DateTime(); // Huidige datum
    $interval = $currentDate->diff($creationDateTime); // Bereken verschil

    // Toon tijd in jaren of dagen afhankelijk van hoe lang de instance bestaat
    if ($interval->y > 0) {
        $instanceAge = $interval->y . ''; // Tijdsduur in jaren
    } else {
        $instanceAge = $interval->days . ''; // Tijdsduur in dagen
    }
} else {
    $instanceAge = 'Onbekend'; // Als creation_date ontbreekt
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instance Info - <?php echo htmlspecialchars($instanceName); ?></title>
    <link rel="stylesheet" href="static/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <style>
.button {
    padding: 10px 15px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
}

.instance-info {
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    text-align: center; /* Titel gecentreerd */
}

.instance-info h3 {
    margin: 0 0 20px 0; /* Ruimte onder de titel */
}

.info-content {
    text-align: left; /* Informatie links uitlijnen */
}

.progress-container {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    margin-bottom: 20px;
}

.progress-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 10px solid #ccc;
    position: relative;
    background-color: #fff;
}

.progress-inner {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    clip: rect(0, 120px, 120px, 60px);
}

.progress-fill {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: transparent; /* Verwijder de groene kleur */
    clip: rect(0, 60px, 120px, 0);
    transform: rotate(0deg);
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
}

.instance-link {
    font-weight: bold;
    color: #337ab7;
    text-decoration: none;
}

.file-list li {
    list-style: none;
    margin: 5px 0;
    display: flex;
    align-items: center;
}

.file-list li .icon {
    margin-right: 10px;
    font-weight: bold;
}

.icon-folder {
    color: #f4a261; /* Kleur voor mappen */
}

.icon-file {
    color: #2a9d8f; /* Kleur voor bestanden */
}

.breadcrumb {
    background: linear-gradient(to right, #f8f9fa, #e9ecef); /* Lichtere achtergrondkleuren passend bij je thema */
    padding: 6px 10px; /* Kleinere padding voor een compacter uiterlijk */
    border-radius: 5px; /* Iets afgeronde hoeken voor zachter uiterlijk */
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); /* Zeer subtiele schaduw voor diepte */
    display: inline-block;
    font-size: 14px; /* Kleinere tekst */
    font-family: 'Arial', sans-serif;
    color: #333; /* Neutrale tekstkleur */
    margin-bottom: 15px; /* Beetje ruimte onder de breadcrumb */
}

.breadcrumb a {
    text-decoration: none;
    color: #007bff; /* Je bestaande blauwe kleur voor links */
    font-weight: bold;
    transition: color 0.2s ease; /* Snellere overgang voor hover */
}

.breadcrumb a:hover {
    color: #0056b3; /* Donkerder blauw bij hover */
}

.breadcrumb i {
    margin: 0 4px;
    color: #6c757d; /* Subtiel grijs voor de chevron */
    font-size: 12px; /* Kleinere iconen */
}

.breadcrumb a:first-child {
    color: #28a745; /* Groene kleur voor de 'root' map */
}

.breadcrumb a:first-child:hover {
    color: #218838; /* Donkerder groen bij hover */
}

.breadcrumb a {
    padding: 3px 6px;
    border-radius: 4px;
    background-color: #f1f1f1; /* Zachte achtergrond voor links */
}

.breadcrumb a:hover {
    background-color: #e2e6ea; /* Lichte hover-achtergrond */
}

.breadcrumb {
    border: 1px solid #ddd; /* Dunne rand voor definitie */
    margin-bottom: 10px; /* Minder ruimte onder breadcrumb */
}

/* Nieuwe stijl voor de scheidingslijn */
.separator {
    height: 1px; /* Dunne lijn */
    background-color: #ccc; /* Grijze kleur */
    margin: 20px 0; /* Ruimte boven en onder de lijn */
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
            <h2>Panel van <?php echo htmlspecialchars($instanceName); ?></h2>

            <div class="instance-info">
    <h3>Instance informatie</h3>

    <div class="progress-container">
        <!-- Aantal Bestanden Progress Circle -->
        <div class="progress-circle">
            <div class="progress-inner">
                <div class="progress-fill" style="transform: rotate(<?php echo ($fileCount / 100) * 360; ?>deg);"></div>
            </div>
            <div class="progress-text" style="top: 40%;"><?php echo htmlspecialchars($fileCount); ?></div>
            <div class="progress-text" style="top: 60%;">Bestanden</div>
        </div>

        <!-- Tijdsduur van de instance -->
        <div class="progress-circle">
            <div class="progress-inner">
                <div class="progress-fill" style="transform: rotate(<?php echo min($interval->days / 365, 1) * 360; ?>deg);"></div>
            </div>
            <div class="progress-text" style="top: 40%;"><?php echo htmlspecialchars($instanceAge); ?></div>
            <div class="progress-text" style="top: 60%;">Dagen</div>
        </div>
    </div>

    <!-- Scheidingslijn -->
    <div class="separator"></div>

    <!-- Info-content div voor links uitlijnen -->
    <div class="info-content">
        <!-- Instance Adres -->
        <p><strong>Instance adres:</strong> 
            <a href="https://oshosting.totalh.net/instances/<?php echo urlencode($instance['name']); ?>/index" class="instance-link">
                https://oshosting.totalh.net/instances/<?php echo urlencode($instance['name']); ?>/index
            </a>
        </p>

        <!-- Instance Naam -->
        <p><strong>Instance Naam:</strong> 
            <a href="javascript:void(0);" id="instance-name" onclick="copyInstanceName()" class="instance-link">
                <?php echo htmlspecialchars($instance['name']); ?>
            </a>
        </p>

        <!-- Aanmaakdatum -->
        <p><strong>Aanmaakdatum:</strong> 
            <a href="javascript:void(0);" id="creation-date" onclick="copyCreationDate()" class="instance-link">
                <?php echo htmlspecialchars($instance['creation_date']); ?>
            </a>
        </p>

        <!-- Eigenaar -->
        <p><strong>Eigenaar:</strong> 
            <a href="javascript:void(0);" id="instance-owner" onclick="copyInstanceOwner()" class="instance-link">
                <?php echo htmlspecialchars($instance['owner']); ?>
            </a>
        </p>
    </div>
</div>


<script>
function copyInstanceName() {
    const instanceName = document.getElementById('instance-name').innerText;
    navigator.clipboard.writeText(instanceName)
        .then(() => {
            alert('Instance naam gekopieerd naar klembord!');
        })
        .catch(err => {
            console.error('Fout bij het kopiëren: ', err);
        });
}

function copyCreationDate() {
    const creationDate = document.getElementById('creation-date').innerText;
    navigator.clipboard.writeText(creationDate)
        .then(() => {
            alert('Aanmaakdatum gekopieerd naar klembord!');
        })
        .catch(err => {
            console.error('Fout bij het kopiëren: ', err);
        });
}

function copyInstanceOwner() {
    const instanceOwner = document.getElementById('instance-owner').innerText;
    navigator.clipboard.writeText(instanceOwner)
        .then(() => {
            alert('Eigenaar gekopieerd naar klembord!');
        })
        .catch(err => {
            console.error('Fout bij het kopiëren: ', err);
        });
}
</script>
            
            <?php if (isset($uploadMessage)) echo "<p>$uploadMessage</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <label for="fileToUpload">Bestand uploaden:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
                <button type="submit">Upload Bestand</button>
            </form>

            <h4>Maak nieuwe map:</h4>
            <?php if (isset($newFolderMessage)) echo "<p>$newFolderMessage</p>"; ?>
            <form method="POST">
               <input type="text" name="newFolderName" placeholder="Naam van de map" required>
               <button type="submit">Nieuwe Map</button>
        </form>

            <h4>Maak nieuw bestand:</h4>
            <?php if (isset($newFileMessage)) echo "<p>$newFileMessage</p>"; ?>
            <form method="POST">
                <input type="text" name="newFileName" placeholder="Naam van het bestand met extensie" required>
                <button type="submit">Nieuw Bestand</button>
            </form>
<h4>Locatie:</h4>
<p class="breadcrumb">
    <?php echo generateBreadcrumbs($instanceName, $subDir); ?>
</p>

<h4>Bestanden:</h4>
<ul class="file-list">
    <?php foreach ($files as $file): ?>
        <li>
            <?php if (is_dir("instances/{$instanceName}/{$subDir}{$file}")): ?>
                <!-- Icoon voor een map -->
                <span class="icon icon-folder">📁</span>
                <a href="instanceinfo.php?name=<?php echo urlencode($instanceName); ?>&subdir=<?php echo urlencode($subDir . $file . '/'); ?>">
                    <?php echo htmlspecialchars($file); ?>
                </a>
            <?php else: ?>
                <!-- Icoon voor een bestand -->
                <span class="icon icon-file">📄</span>
                <a href="edit_file.php?name=<?php echo urlencode($instanceName); ?>&file=<?php echo urlencode($subDir . $file); ?>">
                    <?php echo htmlspecialchars($file); ?>
                </a>
            <?php endif; ?>
            <a href="instanceinfo.php?name=<?php echo urlencode($instanceName); ?>&delete=<?php echo urlencode($subDir . $file); ?>" class="delete" onclick="return confirm('Weet je zeker dat je dit bestand wilt verwijderen?');">Verwijder</a>
        </li>
    <?php endforeach; ?>
</ul>
        </div>
    </div>
</body>

</html>