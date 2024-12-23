<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes') or die(mysqli_error($conn));

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prénom'];
    $ville = $_POST['ville'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe']; // Mot de passe non haché

    $stmt = $conn->prepare("INSERT INTO clients (nom, prénom, mot_de_passe, ville, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nom, $prenom, $mot_de_passe, $ville, $email);

    if ($stmt->execute()) {
        echo "Inscription réussie!";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
