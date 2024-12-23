<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes');

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
    SELECT g.nom, c.id, c.nom, c.prénom, c.ville, c.email 
    FROM groupes g
    JOIN client_groupe cg ON g.id = cg.groupe_id
    JOIN clients c ON cg.client_id = c.id
    WHERE cg.groupe_id = (SELECT groupe_id FROM client_groupe WHERE client_id = ?)
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($groupe_nom, $member_id, $member_nom, $member_prenom, $member_ville, $member_email);

$members = [];
while ($stmt->fetch()) {
    $members[] = [
        'id' => $member_id,
        'nom' => $member_nom,
        'prenom' => $member_prenom,
        'ville' => $member_ville,
        'email' => $member_email
    ];
}

$stmt->close();

// Générer le calendrier
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
$calendrier = [];

foreach ($jours as $jour) {
    // Choisir aléatoirement un membre pour le retrait
    $random_key = array_rand($members);
    $membre_retrait = $members[$random_key];
    
    $calendrier[] = [
        'jour' => $jour,
        'type' => 'retrait',
        'membre' => $membre_retrait
    ];
    
    // Tous les membres peuvent déposer
    foreach ($members as $member) {
        $calendrier[] = [
            'jour' => $jour,
            'type' => 'dépôt',
            'membre' => $member
        ];
    }
}

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
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .client-info {
            flex: 1;
            margin-right: 20px;
        }
        .client-info h1 {
            margin-top: 0;
        }
        .calendar {
            flex: 2;
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
        .buttons a {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(45deg, #009879, #007f65);
            color: #ffffff;
            border-radius: 25px;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .buttons a:hover {
            background: linear-gradient(45deg, #007f65, #009879);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        .calendar h2 {
            text-align: center;
            margin-top: 0;
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
            <div class="buttons">
                <a href="logout.php">Se déconnecter</a>
                <a href="client_dashboard.php">Retour</a>
            </div>
        </div>

        <div class="calendar">
            <h2>CALENDRIER</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Type</th>
                        <th>Membre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calendrier as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['jour']); ?></td>
                        <td><?php echo htmlspecialchars($event['type']); ?></td>
                        <td><?php echo htmlspecialchars($event['membre']['prenom'] . ' ' . $event['membre']['nom']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
