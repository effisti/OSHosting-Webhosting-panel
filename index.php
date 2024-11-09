
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - OSHosting</title>
    <link rel="stylesheet" href="static/styles.css">
    <style>
    /* Basis reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f7fa;
    color: #333;
}

/* Header styling */
.banner {
    background: linear-gradient(to right, #0084ff, #00aaff); /* Kleurverloop van blauw naar lichtblauw */
    color: white; /* Kleur van de tekst */
    text-align: center;
    padding: 40px 20px; /* Grotere padding voor een grotere banner */
    font-size: 1.2em; /* Iets grotere tekst voor de banner */
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Schaduw voor een nette uitstraling */
}

.banner h1 {
    margin: 0;
    font-size: 2.5em; /* Grotere tekst voor de titel */
}

.banner p {
    margin: 10px 0 0;
    font-size: 1.2em; /* Kleinere tekst voor de beschrijving */
}

.banner nav a {
    background-color: white;
    color: #007bff;
    padding: 10px 20px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
}

.banner nav a:hover {
    background-color: #0056b3;
    color: white;
}

/* Main content styling */
.content-container {
    padding: 40px;
    text-align: center;
}

h1, h2 {
    margin-bottom: 20px;
}

h1 {
    color: white;
}

p {
    font-size: 1.2rem;
    line-height: 1.6;
}

/* Footer styling */
footer {
    background-color: #343a40;
    color: white;
    padding: 10px;
    text-align: center;
    position: fixed;
    bottom: 0;
    width: 100%;
}
</style>
</head>
<body>
    <header>
        
        <div class="banner">
            <h1>OSHosting</h1>
            <nav>
                <a href="plans" class="btn-primary plans-button">Plans</a>
                <a href="auth/login" class="btn-primary">Inloggen</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="content-container">
            <h2>Welkom bij OSHosting!!</h2>
            <p>OSHosting is een hosting website waar jij al vanaf 0 euro een website op kan hosten.</p>
            <p>Voor meer informatie, kunt u deze Discord server joinen: <a href="https://discord.gg/dugfhHCXrN">https://discord.gg/dugfhHCXrN</a></p>
            
        </div>
    </main>

    <footer>
        <p>&copy; 2024 OSHosting. Alle rechten voorbehouden.</p>
    </footer>
</body>
</html>