<?php
include '../models/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST['pseudo'];
    
    // Récupérer la catégorie si elle existe
    $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
    
    // Le pseudo existe déjà
    $check_sql = "SELECT id_joueur FROM joueur WHERE pseudo = :pseudo";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindParam(':pseudo', $pseudo);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        // Le pseudo existe, on récupère l'ID du joueur
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        $id_joueur = $result['id_joueur'];
    } else {
        // Le pseudo n'existe pas, créer un nouveau joueur
        $sql = "INSERT INTO joueur (pseudo, score) VALUES (:pseudo, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pseudo', $pseudo);
        
        if ($stmt->execute()) {
            // Récupérer l'ID du joueur nouvellement créé
            $id_joueur = $pdo->lastInsertId();
        } else {
            echo "Erreur: " . $stmt->errorInfo()[2];
            exit;
        }
    }
    
    // Construire l'URL de redirection
    $redirect_url = '../views/question.php?pseudo=' . urlencode($pseudo) . '&id_joueur=' . urlencode($id_joueur);
    
    // Ajouter la catégorie à l'URL si elle existe
    if (!empty($categorie)) {
        $redirect_url .= '&categorie=' . urlencode($categorie);
    }
    
    // Rediriger vers la page des questions
    header('Location: ' . $redirect_url);
    exit;
}
?>