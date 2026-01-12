<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style.css">
    <title>Quiz</title>
</head>
<body>
<?php
    include '../models/db.php';
    $sql_cat = "SELECT DISTINCT categorie FROM question";
    $stmt_cat = $pdo->prepare($sql_cat);
    $stmt_cat->execute();
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    
    $categorie_selectionnee = isset($_GET['categorie']) ? $_GET['categorie'] : '';

    echo "<div class='categories'>";
    //ici on affiche les catégories pour le styles 
    //mais elle ne sont pas cliquables quand on est sur la page de vérification pour eviter les bugs
    echo "<span class='button_cat'>Toutes</span>";

    foreach ($categories as $categorie) {
        $active_class = ($categorie_selectionnee == $categorie['categorie']) ? ' active' : '';
        echo "<span class='button_cat" . $active_class . "'>" . htmlspecialchars($categorie['categorie']) . "</span>";
    }
    echo "</div>";
?>

<?php
include '../models/db.php';
// Récupération des paramètre
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    if (isset($_GET['pseudo']) && isset($_GET['id_joueur']) && isset($_GET['categorie'])) {
        $pseudo = $_GET['pseudo'];
        $id_joueur = $_GET['id_joueur'];
        $categorie = $_GET['categorie'];
    }
    
    // Sélectionne la bonne réponse et le nombre de points de la question
    $sql = "SELECT id_question, reponse_vrai, nb_point FROM question WHERE question = :question";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':question', $question);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $id_question = $result['id_question'];
        $nb_point = $result['nb_point'];
        $bonne_reponse = $result['reponse_vrai'];

        // Enregistre la question dans la table joueur_question
        $sql = "INSERT INTO joueur_question (id_joueur, id_question) VALUES (:id_joueur, :id_question)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_joueur', $id_joueur);
        $stmt->bindParam(':id_question', $id_question);
        $stmt->execute();

        if ($answer == $bonne_reponse) {
            // La réponse est correcte
            $sql = "UPDATE joueur SET score = score + :nb_point WHERE id_joueur = :id_joueur";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nb_point', $nb_point);
            $stmt->bindParam(':id_joueur', $id_joueur);
            $stmt->execute();
            
            // Redirection vers une autre question, en conservant la catégorie
            $redirect_url = '../views/question.php?pseudo=' . urlencode($pseudo) . '&id_joueur=' . urlencode($id_joueur);
            if (!empty($categorie)) {
                $redirect_url .= '&categorie=' . urlencode($categorie);
            }
            header('Location: ' . $redirect_url);
            exit;
        } else {
            // Mauvaise réponse ou timeout
            echo "<div style='text-align:center; margin-top:50px;'>";
            if ($answer === '__TIMEOUT__') {
                echo "<h2>Temps écoulé !</h2>";
                echo "<p>La bonne réponse était : " . htmlspecialchars($bonne_reponse) . "</p>";
            } else {
                echo "<h2>Mauvaise réponse</h2>";
                echo "<p>La bonne réponse était: " . htmlspecialchars($bonne_reponse) . "</p>";
            }
            // Lien vers la question suivante, en conservant la catégorie
            $next_url = '../views/question.php?pseudo=' . urlencode($pseudo) . '&id_joueur=' . urlencode($id_joueur);
            if (!empty($categorie)) {
                $next_url .= '&categorie=' . urlencode($categorie);
            }
            echo "<a href='" . $next_url . "' class='button_cat'>Question suivante</a>";
            echo "</div>";
        }
    } else {
        echo "Question non trouvée.";
    }
}
?>

</body>
</html>

