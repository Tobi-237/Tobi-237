<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'epargnes');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ajouter_groupe'])) {
        $client_id = $_POST['client_id'];
        $groupe_id = $_POST['groupe_id'];

        // Ajouter le client au groupe
        $stmt = $conn->prepare("INSERT INTO client_groupe (client_id, groupe_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $client_id, $groupe_id);
        if ($stmt->execute()) {
            echo "Client ajouté au groupe avec succès!";
        } else {
            echo "Erreur lors de l'ajout du client au groupe.";
        }

        $stmt->close();
    } elseif (isset($_POST['supprimer_client'])) {
        $client_id = $_POST['client_id'];
        $groupe_id = $_POST['groupe_id'];

        // Supprimer le client du groupe
        $stmt = $conn->prepare("DELETE FROM client_groupe WHERE client_id = ? AND groupe_id = ?");
        $stmt->bind_param("ii", $client_id, $groupe_id);
        if ($stmt->execute()) {
            echo "Client supprimé du groupe avec succès!";
        } else {
            echo "Erreur lors de la suppression du client du groupe.";
        }

        $stmt->close();
    } elseif (isset($_POST['modifier_privileges'])) {
        $client_id = $_POST['client_id'];
        $peut_ecrire = isset($_POST['peut_ecrire']) ? 1 : 0;

        // Mettre à jour les privilèges du client
        $stmt = $conn->prepare("UPDATE client_groupe SET peut_ecrire = ? WHERE client_id = ?");
        $stmt->bind_param("ii", $peut_ecrire, $client_id);
        if ($stmt->execute()) {
            echo "Privilèges modifiés avec succès!";
        } else {
            echo "Erreur lors de la modification des privilèges.";
        }

        $stmt->close();
    }
}

// Récupérer les clients et les groupes pour le formulaire
$clients = $conn->query("SELECT id, nom, prénom FROM clients");
$groupes = $conn->query("SELECT id, nom FROM groupes");

// Récupérer les groupes et les clients associés pour l'affichage
$groupes_clients = $conn->query("
    SELECT g.id as groupe_id, g.nom as groupe_nom, c.id as client_id, c.nom as client_nom, c.prénom as client_prenom
    FROM groupes g
    LEFT JOIN client_groupe cg ON g.id = cg.groupe_id
    LEFT JOIN clients c ON cg.client_id = c.id
    ORDER BY g.nom, c.nom
");

// Récupérer les clients et leurs privilèges
$clients_privileges = $conn->query("
    SELECT c.id, c.nom, c.prénom, cg.peut_ecrire 
    FROM clients c
    JOIN client_groupe cg ON c.id = cg.client_id
    ORDER BY c.nom, c.prénom
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }
        .left, .right {
            width: 48%;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2 {
            color: #009879;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #009879;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f3f3f3;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Ajouter un client à un groupe</h1>
            <form action="admin_login.php" method="post">
                <label for="client_id">Client:</label>
                <select id="client_id" name="client_id" required>
                    <?php while ($client = $clients->fetch_assoc()): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['nom'] . ' ' . $client['prénom']); ?></option>
                    <?php endwhile; ?>
                </select><br>

                <label for="groupe_id">Groupe:</label>
                <select id="groupe_id" name="groupe_id" required>
                    <?php while ($groupe = $groupes->fetch_assoc()): ?>
                        <option value="<?php echo $groupe['id']; ?>"><?php echo htmlspecialchars($groupe['nom']); ?></option>
                    <?php endwhile; ?>
                </select><br>

                <input type="submit" name="ajouter_groupe" value="Ajouter au groupe">
            </form>

            <h2>Groupes et Clients</h2>
            <table>
                <thead>
                    <tr>
                        <th>Groupe</th>
                        <th>Client</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_groupe = null;
                    while ($row = $groupes_clients->fetch_assoc()):
                        if ($current_groupe !== $row['groupe_nom']):
                            $current_groupe = $row['groupe_nom'];
                    ?>
                        <tr>
                            <td colspan="3"><strong><?php echo htmlspecialchars($current_groupe); ?></strong></td>
                        </tr>
                    <?php endif; ?>
                        <tr>
                            <td></td>
                            <td><?php echo htmlspecialchars($row['client_nom'] . ' ' . $row['client_prenom']); ?></td>
                            <td>
                                <?php if ($row['client_id']): ?>
                                <form action="admin_login.php" method="post" style="display:inline;">
                                    <input type="hidden" name="client_id" value="<?php echo $row['client_id']; ?>">
                                    <input type="hidden" name="groupe_id" value="<?php echo $row['groupe_id']; ?>">
                                    <input type="submit" name="supprimer_client" value="Supprimer">
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="right">
            <h1>Gestion des Privilèges de Discussion</h1>
            <form action="admin_login.php" method="post">
                <label for="client_id">Client:</label>
                <select id="client_id" name="client_id" required>
                    <?php 
                    // Reset the result pointer and fetch data again for privileges form
                    $clients->data_seek(0);
                    while ($client = $clients->fetch_assoc()): ?>
                        <option value="<?php echo $client['id']; ?>">
                            <?php echo htmlspecialchars($client['nom'] . ' ' . $client['prénom']); ?>
                        </option>
                    <?php endwhile; ?>            
            </select><br>

            <label for="peut_ecrire">Peut écrire:</label>
            <input type="checkbox" id="peut_ecrire" name="peut_ecrire"><br>

            <input type="submit" name="modifier_privileges" value="Modifier les Privilèges">
        </form>

        <h2>Liste des Clients et leurs Privilèges</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Peut écrire</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($client = $clients_privileges->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['nom'] . ' ' . $client['prénom']); ?></td>
                    <td><?php echo $client['peut_ecrire'] ? 'Oui' : 'Non'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="lagoutprincipal.php">Déconnexion</a>
    </div>
</body>
</html>
