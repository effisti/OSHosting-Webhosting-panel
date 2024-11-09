<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Haal de instance naam en bestandsnaam op
$instanceName = $_GET['name'] ?? '';
$fileName = $_GET['file'] ?? '';

// Zorg ervoor dat de bestandsnaam geldig is
if (empty($instanceName) || empty($fileName)) {
    header('Location: instances.php');
    exit;
}

// Bepaal het pad van het bestand
$filePath = "instances/{$instanceName}/{$fileName}";

// Controleer of het bestand bestaat
if (!file_exists($filePath)) {
    header('Location: instanceinfo.php?name=' . urlencode($instanceName));
    exit;
}

// Laad de inhoud van het bestand
$fileContent = file_get_contents($filePath);

// Sla de wijzigingen op
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newContent = $_POST['fileContent'];
    file_put_contents($filePath, $newContent);
    header('Location: instanceinfo.php?name=' . urlencode($instanceName));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerk Bestand - <?php echo htmlspecialchars($fileName); ?></title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    
    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    
    <link rel="stylesheet" href="static/styles.css">
    
    <style>
        .editor-container {
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <h2>Bewerk Bestand: <?php echo htmlspecialchars($fileName); ?></h2>
        <form method="POST">
            <textarea id="codeEditor" name="fileContent" rows="20" style="width: 100%;"><?php echo htmlspecialchars($fileContent); ?></textarea>
            <button type="submit">Opslaan</button>
        </form>
        <a href="instanceinfo.php?name=<?php echo urlencode($instanceName); ?>" class="back-button">Terug naar instance</a>
    </div>

    <!-- CodeMirror JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/php/php.min.js"></script>

    <script>
        // Initialize CodeMirror
        var editor = CodeMirror.fromTextArea(document.getElementById("codeEditor"), {
            lineNumbers: true,
            mode: "text/x-php", // Default mode
            theme: "default",
            matchBrackets: true
        });

        // Dynamically set the mode based on the file extension
        var fileName = "<?php echo htmlspecialchars($fileName); ?>";
        if (fileName.endsWith(".html")) {
            editor.setOption("mode", "text/html");
        } else if (fileName.endsWith(".css")) {
            editor.setOption("mode", "text/css");
        } else if (fileName.endsWith(".js")) {
            editor.setOption("mode", "text/javascript");
        } else if (fileName.endsWith(".php")) {
            editor.setOption("mode", "text/x-php");
        } else if (fileName.endsWith(".htaccess")) {
            editor.setOption("mode", "text/plain");
        }
    </script>
</body>
</html>
