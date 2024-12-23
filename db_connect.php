<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
