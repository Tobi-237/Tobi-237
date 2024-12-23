<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes') or die(mysqli_error($conn));

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$client_id = $_SESSION['client_id'];
$groupe_id = 1; // Remplacez par l'ID de votre groupe

// Vérifiez si le client a les privilèges pour écrire
$stmt_privilege = $conn->prepare("SELECT peut_ecrire FROM client_groupe WHERE client_id = ? AND groupe_id = ?");
$stmt_privilege->bind_param("ii", $client_id, $groupe_id);
$stmt_privilege->execute();
$stmt_privilege->bind_result($peut_ecrire);
$stmt_privilege->fetch();
$stmt_privilege->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $peut_ecrire) {
    $message = $conn->real_escape_string($_POST['message']);
    $stmt_message = $conn->prepare("INSERT INTO messages (client_id, groupe_id, contenu) VALUES (?, ?, ?)");
    $stmt_message->bind_param("iis", $client_id, $groupe_id, $message);
    $stmt_message->execute();
    $stmt_message->close();
    header("Location: discutionbuton.php");
    exit();
}

// Récupérer les messages du groupe
$stmt_messages = $conn->prepare("SELECT clients.nom, clients.prénom, messages.contenu, messages.timestamp 
                               FROM clients 
                               JOIN messages ON clients.id = messages.client_id 
                               WHERE messages.groupe_id = ? 
                               ORDER BY messages.timestamp DESC");
$stmt_messages->bind_param("i", $groupe_id);
$stmt_messages->execute();
$stmt_messages->bind_result($nom, $prenom, $contenu, $timestamp);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background-color: #009879;
            padding: 10px 0;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        .titre {
            text-align: center;
            color: #009879;
            margin: 20px 0;
        }
        #sect1 {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f4f4f9;
            border-left: 5px solid #009879;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .message-author {
            font-weight: bold;
        }
        .message-timestamp {
            color: #999;
            font-size: 0.9em;
        }
        .message-content {
            white-space: pre-wrap;
        }
        form {
            margin-top: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #009879;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #007f65;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="logindiscussion.php">Login</a></li>
            <?php
            if (isset($_SESSION['nom'])) {
                echo '<li><a href="logout.php">Déconnexion</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>
<section>
    <h1 class="titre">Bienvenue dans notre forum !</h1>
</section>
<section id="sect1">
    <?php
    while ($stmt_messages->fetch()) {
        echo '<div class="message">';
        echo '<div class="message-header">';
        echo '<span class="message-author">' . htmlspecialchars($nom) . ' ' . htmlspecialchars($prenom) . '</span>';
        echo '<span class="message-timestamp">' . htmlspecialchars($timestamp) . '</span>';
        echo '</div>';
        echo '<div class="message-content">' . nl2br(htmlspecialchars($contenu)) . '</div>';
        echo '</div>';
    }
    $stmt_messages->close();
    ?>
    <?php if ($peut_ecrire): ?>
    <form action="discutionbuton.php" method="post">
        <textarea name="message" placeholder="Votre message" id="zmessage" required></textarea>
        <input type="submit" name="envoyer" value="Envoyer" class="btn2">
        <a href="client_dashboard.php">Retour</a>
    </form>
    <?php else: ?>
    <p>Vous n'avez pas les privilèges pour écrire dans ce forum.</p>
    <a href="client_dashboard.php">Retour</a>
    <?php endif; ?>
</section>
</body>
</html>
