<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes') or die(mysqli_error($conn));

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email_connexion'];
    $mot_de_passe = $_POST['mot_de_passe_connexion'];

    $stmt = $conn->prepare("SELECT id, mot_de_passe FROM clients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($client_id, $mot_de_passe_stocke);
        $stmt->fetch();
        if ($mot_de_passe === $mot_de_passe_stocke) {
            // Stocker l'ID du client dans la session
            $_SESSION['client_id'] = $client_id;

            // Vérifier si le client appartient à un groupe
            $stmt_groupe = $conn->prepare("SELECT g.nom FROM groupes g 
                                            JOIN client_groupe cg ON g.id = cg.groupe_id 
                                            WHERE cg.client_id = ?");
            $stmt_groupe->bind_param("i", $client_id);
            $stmt_groupe->execute();
            $stmt_groupe->store_result();

            if ($stmt_groupe->num_rows > 0) {
                $stmt_groupe->bind_result($groupe_nom);
                $stmt_groupe->fetch();
                // Stocker le nom du groupe dans la session
                $_SESSION['groupe_nom'] = $groupe_nom;
            } else {
                $_SESSION['groupe_nom'] = "Un groupe vous sera attribué dans un court délai.";
            }

            $stmt_groupe->close();
            
            // Redirection vers le tableau de bord du client
            header("Location: client_dashboard.php");
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun compte trouvé avec cet email.";
    }

    $stmt->close();
}

$conn->close();
?>
