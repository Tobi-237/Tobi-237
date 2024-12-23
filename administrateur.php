<?php
session_start();

// Si l'administrateur est déjà connecté, rediriger vers la page admin_dashboard.php
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inclure le fichier de connexion à la base de données
    require 'db_connect.php';

    $nom = $_POST['nom'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Préparer et exécuter la requête SQL pour vérifier les informations de connexion
    $stmt = $conn->prepare("SELECT id FROM administrateurs WHERE nom = ? AND mot_de_passe = ?");
    $stmt->bind_param("ss", $nom, $mot_de_passe);
    $stmt->execute();
    $stmt->bind_result($admin_id);
    $stmt->fetch();

    if ($admin_id) {
        // Connexion réussie
        $_SESSION['admin_id'] = $admin_id;
        header("Location: administrateur.php");
        exit();
    } else {
        // Connexion échouée
        $error_message = "Nom d'utilisateur ou mot de passe incorrect";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('OIP.jpeg');
            background-position: center;
            background-size: cover;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        h1 {
            color: #009879;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #009879;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #007f65;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Connexion Administrateur</h1>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="administrateur.php" method="post">
            <input type="text" name="nom" placeholder="Nom d'utilisateur" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <input type="submit" value="Se connecter">
            <a href="lagoutprincipal.php">Déconnexion</a>
        </form>

    </div>
</body>
</html>
