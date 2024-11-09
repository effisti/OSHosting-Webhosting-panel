<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onze Hostingplannen - OSHosting</title>
    <link rel="stylesheet" href="static/styles.css"> <!-- Verwijzing naar externe stylesheet -->
    <style>
        /* Grid layout for the plans */
        .plans-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 40px;
        }

        .plan-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .plan-card img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .plan-card h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .plan-card p {
            font-size: 1.2em;
            margin-bottom: 15px;
        }

        .plan-card .price {
            font-size: 1.3em;
            color: #28a745;
            font-weight: bold;
        }

        .plan-card .add-to-cart {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .plan-card .add-to-cart:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Header section, stijl uit styles.css -->
    <header>
        <div class="banner">
            <h1>OSHosting</h1>
            <nav>
                <a href="home" class="btn-primary">Home</a>
                <a href="auth/login" class="btn-primary">Inloggen</a>
            </nav>
        </div>
    </header>

    <!-- Main content: Hosting plans -->
    <main>
        <div class="bedankt-aankoop">
            <h2>Bedankt voor je aankoop</h2>
            <p>Maak een ticket aan met een screenshot van je aankoop</p>
            <p>om je aankoop te ontvangen</p>
            </div>
        </div>
    </main>

    <!-- Footer section, stijl uit styles.css -->
    <footer>
        <p>&copy; 2024 OSHosting. Alle rechten voorbehouden.</p>
    </footer>

</body>
</html>