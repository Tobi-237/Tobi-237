<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes') or die(mysqli_error($conn));

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les informations du client
$client_id = $_SESSION['client_id'];
$stmt = $conn->prepare("SELECT nom, prénom, ville, email FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($nom, $prenom, $ville, $email);
$stmt->fetch();
$stmt->close();

// Récupérer le nom du groupe et les membres du groupe
$stmt = $conn->prepare("
    SELECT g.nom, c.nom, c.prénom, c.ville, c.email 
    FROM groupes g
    JOIN client_groupe cg ON g.id = cg.groupe_id
    JOIN clients c ON cg.client_id = c.id
    WHERE cg.groupe_id = (SELECT groupe_id FROM client_groupe WHERE client_id = ?)
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($groupe_nom, $member_nom, $member_prenom, $member_ville, $member_email);

$members = [];
while ($stmt->fetch()) {
    $members[] = [
        'nom' => $member_nom,
        'prenom' => $member_prenom,
        'ville' => $member_ville,
        'email' => $member_email
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design.css">
    <?php


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes') or die(mysqli_error($conn));

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les informations du client
$client_id = $_SESSION['client_id'];
$stmt = $conn->prepare("SELECT nom, prénom, ville, email FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($nom, $prenom, $ville, $email);
$stmt->fetch();
$stmt->close();

// Récupérer le nom du groupe et les membres du groupe
$stmt = $conn->prepare("
    SELECT g.nom, c.nom, c.prénom, c.ville, c.email 
    FROM groupes g
    JOIN client_groupe cg ON g.id = cg.groupe_id
    JOIN clients c ON cg.client_id = c.id
    WHERE cg.groupe_id = (SELECT groupe_id FROM client_groupe WHERE client_id = ?)
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($groupe_nom, $member_nom, $member_prenom, $member_ville, $member_email);

$members = [];
while ($stmt->fetch()) {
    $members[] = [
        'nom' => $member_nom,
        'prenom' => $member_prenom,
        'ville' => $member_ville,
        'email' => $member_email
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design.css">
    <title>Tableau de Bord Client</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .client-info {
            margin-bottom: 20px;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }
        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }
        .styled-table th, .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .styled-table tbody tr {
            border-bottom: 1px solid #ddd;
        }
        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }
        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }
        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="client-info">
            <h1>Bienvenue, <?php echo htmlspecialchars($prenom . ' ' . $nom); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <p>Ville: <?php echo htmlspecialchars($ville); ?></p>
            <p>Groupe: <?php echo htmlspecialchars($groupe_nom); ?></p>
            <a href="logout.php">Se déconnecter</a>
            
        </div>

        <div class="group-members">
            <h2>Les membres de votre groupe</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Ville</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['nom']); ?></td>
                        <td><?php echo htmlspecialchars($member['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($member['ville']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="container">
        <!-- Autres contenus -->
        <div class="buttons">
            <a href="planning.php">Planning</a>
            <a href="logindiscussion.php">Discussion</a>
            <a href="inscription.php">Mode de payement </a>
        </div>
              
        </div>
    </div>
</body>
</html>
