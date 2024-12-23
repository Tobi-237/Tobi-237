<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription à un Cours - Totine237</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #maincontain {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .mainitem {
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #666;
        }
        img.logo {
            max-width: 100px;   
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            color: #333;
            text-align: left;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .divsubmit input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
        }
        .divsubmit input[type="submit"]:hover {
            background-color: #218838;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div id="maincontain">
        <div class="mainitem">
            <h1>Bienvenue<br> sur <br>Totine237</h1>
            <p>La plateforme de formation en ligne par excellence</p>
        </div>

        <div class="mainitem">
            <form action="traitement.php" method="post">
                <p>
                    <img src="images/logopng.png" alt="logo formation" class="logo"/>
                </p>

                <p>S'inscrire à un cours 😋</p>

                <div>
                    <label for="firstname">Nom:</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Entrez votre nom" required />
                </div>
                <div>
                    <label for="lastname">Prénom:</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Entrez votre prénom" required />
                </div>
                <div>
                    <label for="choicelanguage">Choisir un cours:</label>
                    <select id="choicelanguage" name="choicelanguage" required>
                        <option value="php" selected>PHP</option>
                    </select>
                </div>
                <div class="divsubmit">
                    <input type="submit" value="S'inscrire">
                </div>

                <p>Vous êtes déjà inscrit ? Cliquez <a href="login.html">ici</a> pour vérifier cela.</p>
            </form>
        </div>
    </div>
</body>
</html>
