<?php
include '../models/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style.css"> <!-- Ensure the stylesheet is linked -->
    <title>Question</title>
</head>
<body>
<?php
// Récupération des paramètres
$id_joueur = $_GET['id_joueur'];
$pseudo = $_GET['pseudo'];
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;


// Récupérer le score du joueur
$sql_score = "SELECT score FROM joueur WHERE id_joueur = :id_joueur";
$stmt_score = $pdo->prepare($sql_score);
$stmt_score->bindParam(':id_joueur', $id_joueur);
$stmt_score->execute();
$score = $stmt_score->fetch(PDO::FETCH_ASSOC)['score'];

// Afficher le score du joueur
echo "<div style='text-align: center; margin-bottom: 20px;'>";
echo "<h3>Score actuel : " . htmlspecialchars($score) . " points</h3>";
echo "</div>";

// Affichage des boutons de catégorie
$sql_cat = "SELECT DISTINCT categorie FROM question";
$stmt_cat = $pdo->prepare($sql_cat);
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='categories'>";
// Bouton pour afficher toutes les catégories
echo "<a href='question.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "' class='button_cat'>Toutes</a>";
foreach ($categories as $cat) {
    echo "<a href='question.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "&categorie=" . urlencode($cat['categorie']) . "' class='button_cat'>" . htmlspecialchars($cat['categorie']) . "</a>";
}
echo "</div>";

// Vérif s'il reste des questions dans la catégorie sélectionnée
$sql_check_questions = "
    SELECT COUNT(*) AS total_questions
    FROM question AS q
    LEFT JOIN joueur_question AS jq 
        ON q.id_question = jq.id_question 
        AND jq.id_joueur = :id_joueur
    WHERE jq.id_question IS NULL
";

// Ajout du filtre par catégorie si une catégorie est sélectionnée
if ($categorie) {
    $sql_check_questions .= " AND q.categorie = :categorie";
}

$stmt_check = $pdo->prepare($sql_check_questions);
$stmt_check->bindParam(':id_joueur', $id_joueur);

if ($categorie) {
    $stmt_check->bindParam(':categorie', $categorie);
}
$stmt_check->execute();
$questions_left = $stmt_check->fetch(PDO::FETCH_ASSOC)['total_questions'];

if ($questions_left > 0) {
    // Questions auxquelles le joueur n'a pas encore répondu
    $sql = "
    SELECT q.* 
    FROM question AS q
    LEFT JOIN joueur_question AS jq 
        ON q.id_question = jq.id_question 
        AND jq.id_joueur = :id_joueur
    WHERE jq.id_question IS NULL
    ";
    
    // Ajout du filtre par catégorie si une catégorie est sélectionné
    if ($categorie) {
        $sql .= " AND q.categorie = :categorie";
    }
    // Ajout de la condition pour ne pas afficher les questions déjà répondues
    $sql .= " ORDER BY RAND() LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_joueur', $id_joueur);
    if ($categorie) {
        $stmt->bindParam(':categorie', $categorie);
    }
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<div class='div_question'>";
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        // Timeer de base
        // 20 secondes
        echo "<div class='timer-container'><span id='timer' class='timer'>20</span> secondes restantes</div>";
        
        //mettre le nombre de point de l'autre coté
        echo "<div>";
        echo "<p class='categorie-info'>Catégorie: " . htmlspecialchars($question['categorie']) . "</p>";
        echo "<p class='categorie-info' style='margin-left: auto;'>Points: " . htmlspecialchars($question['nb_point']) . "</p>";
        echo "</div>";
        
        echo "<h2>" . htmlspecialchars($question['question']) . "</h2>";
        // tableau de reponses
        $answers = [
            $question['reponse_vrai'],
            $question['reponse_fausse1'],
            $question['reponse_fausse2'],
            $question['reponse_fausse3']
        ];
        //melanger les reponses pour pas avoir la bonne reponse en premier
        shuffle($answers);
        
        //formulaire pour chaque reponse
        //on envoie la question et la reponse dans le formulaire pour verif
        foreach ($answers as $answer) {
            echo "<form method='post' action='../controllers/verif.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "&categorie=" . urlencode($categorie ?? '') . "' class='answer-form'>";
            echo "<input type='hidden' name='question' value='" . htmlspecialchars($question['question']) . "'>";
            echo "<input type='hidden' name='answer' value='" . htmlspecialchars($answer) . "'>";
            echo "<button class='button_cat' type='submit'>" . htmlspecialchars($answer) . "</button>";
            echo "</form>";
        }
        //formulaire caché pour le timeout
        //est soumis automatiquement quand le temps est écoulé
        echo "<form id='timeoutForm' method='post' style='display:none' action='../controllers/verif.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "&categorie=" . urlencode($categorie ?? '') . "'>";
        echo "<input type='hidden' name='question' value='" . htmlspecialchars($question['question']) . "'>";
        echo "<input type='hidden' name='answer' value='__TIMEOUT__'>";
        echo "</form>";

        echo "</div>";
        ?>
        <script>
        // Timer en secondes
        let tempsRestant = 20;
        const timerSpan = document.getElementById('timer');
        // Met à jour le timer chaque seconde
        const intervalle = setInterval(function() {
            tempsRestant--;
            timerSpan.textContent = tempsRestant;
            if (tempsRestant <= 0) {
                clearInterval(intervalle);
                // Soumet automatiquement le formulaire de timeout
                document.getElementById('timeoutForm').submit();
            }
        }, 1000);

        // Si l'utilisateur répond, on arrête le timer
        document.querySelectorAll('.answer-form').forEach(function(formulaire) {
            formulaire.addEventListener('submit', function() {
                clearInterval(intervalle);
            });
        });
        </script>
        <?php
    } else {
        echo "<h2>Aucune question disponible dans cette catégorie.</h2>";
        echo "<p><a href='question.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "' class='button_cat'>Voir toutes les questions</a></p>";
    }
} else if ($categorie) {
    // Plus de questions dans cette catégorie, mais il peut en rester dans d'autres
    echo "<div class='div_question'>";
    echo "<h2>Vous avez répondu à toutes les questions de la catégorie : " . htmlspecialchars($categorie) . "</h2>";
    echo "<p>Passons aux questions des autres catégories.</p>";
    echo "<p><a href='question.php?pseudo=" . urlencode($pseudo) . "&id_joueur=" . urlencode($id_joueur) . "' class='button_cat'>Continuer</a></p>";
    echo "</div>";
} else {
    // Plus aucune question disponible
    echo "<div class='div_question'>";
    echo "<h2>Bravo, vous avez répondu à toutes les questions !</h2>";
    echo "<p>Votre score final : ";
    $sql_score = "SELECT score FROM joueur WHERE id_joueur = :id_joueur";
    $stmt_score = $pdo->prepare($sql_score);
    $stmt_score->bindParam(':id_joueur', $id_joueur);
    $stmt_score->execute();
    $score = $stmt_score->fetch(PDO::FETCH_ASSOC)['score'];
    echo $score . " points</p>";
    echo "<a href='index.php' class='button_cat'>Recommencer</a>";
    echo "</div>";
}
?>

</body>
</html>