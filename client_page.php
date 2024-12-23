<?php
session_start();
if (!isset($_GET['groupe'])) {
    echo "Erreur : groupe non attribué.";
    exit();
}
$groupe = $_GET['groupe'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Client</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenue sur votre page</h1>
    <p>Votre groupe attribué est : <?php echo htmlspecialchars($groupe); ?></p>
    <a href="logout.php">Déconnexion</a>
</body>
</html>
