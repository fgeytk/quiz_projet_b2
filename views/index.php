<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style.css">
    <script src="script.js"></script>
    <title>Quiz</title>
</head>
<body>
<div class="div_question" style="background: none; box-shadow: none; max-width: 900px; margin: 0 auto; padding: 0;">
    <?php
    include '../models/db.php';
    $sql_cat = "SELECT DISTINCT categorie FROM question";
    $stmt_cat = $pdo->prepare($sql_cat);
    $stmt_cat->execute();
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    // Récupération de la catégorie sélectionnée (si elle existe)
    $categorie_selectionnee = isset($_GET['categorie']) ? $_GET['categorie'] : '';

    echo "<div class='categories'>";
    // Bouton pour toutes les catégories
    echo "<a href='index.php' class='button_cat'>Toutes</a>";
    
    foreach ($categories as $categorie) {
        $active_class = ($categorie_selectionnee == $categorie['categorie']) ? ' active' : '';
        echo "<a href='index.php?categorie=" . urlencode($categorie['categorie']) . "' class='button_cat" . $active_class . "'>" . htmlspecialchars($categorie['categorie']) . "</a>";
    }
    echo "</div>";
    ?>


    <div style="text-align: center; margin-top: 20px;">
        <img src="../public/logo.png" alt="BrainQuest Logo" style="height: 120px; max-width: 95vw;">
    </div>

    
    <fieldset>
        <legend>Pour commencer, entrez votre pseudo ci-dessous.</legend>
    
    <form action="../controllers/connexion.php" method="post" id="formpseudo">
        <input class="input" type="text" name="pseudo" placeholder="Pseudonyme" required>
        
        <?php
        // Si une catégorie est sélectionnée, l'ajouter comme champ caché
        if (!empty($categorie_selectionnee)) {
            echo "<input type='hidden' name='categorie' value='" . htmlspecialchars($categorie_selectionnee) . "'>";
        }
        ?>
        
        <br><br>
        <input class="button_cat" type="submit" value="Commencer">
        <p><small>Si vous avez déjà joué, entrez votre pseudo pour continuer.</small></p>
        
        
    </form>
    </fieldset>

    <!-- podium -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="leaderboard.php" class="button_cat" >Classement</a>
        <h3>Top 3 Joueurs</h3>
        <?php
        $sql_top3 = "SELECT pseudo, score FROM joueur ORDER BY score DESC, pseudo ASC LIMIT 3";
        $stmt_top3 = $pdo->prepare($sql_top3);
        $stmt_top3->execute();
        $top3_players = $stmt_top3->fetchAll(PDO::FETCH_ASSOC);

        if (count($top3_players) > 0) {
            echo "<div style='display: flex; justify-content: center; align-items: flex-end; gap: 20px; flex-wrap: wrap;'>";
            foreach ($top3_players as $index => $player) {
                $height = 100 - ($index * 20); // ajuste la hauteur pour chaque joueur
                echo "<div style='text-align: center;'>";
                echo "<div style='background-color: #98bbff; color: white; width: 80px; height: " . $height . "px; display: flex; align-items: center; justify-content: center; border-radius: 5px;'>";
                echo "<strong>" . htmlspecialchars($player['score']) . "</strong>";
                echo "</div>";
                echo "<p style='margin-top: 10px;'>" . htmlspecialchars($player['pseudo']) . "</p>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>Aucun joueur pour le moment.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>