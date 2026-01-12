<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Leaderboard</title>
</head>
<body>
<div class="div_question" style="background: none; box-shadow: none; max-width: 900px; margin: 0 auto; padding: 0;">
    <h2>Voici le classement des joueurs :</h2>
    <div class="div_question">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #eef5ff; color: #3b82f6;">
                    <th style="padding: 10px; border: 1px solid #ccc;">Position</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Pseudo</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';

                //fetch dasns l'ordre du score
                $sql = "SELECT pseudo, score FROM joueur ORDER BY score DESC, pseudo ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $position = 1;
                // j'ai finalement pas mis le gold silver et bronze car c'était tres laid
                foreach ($players as $player) {
                    $row_class = '';
                    if ($position == 1) {
                        $row_class = 'gold';
                    } elseif ($position == 2) {
                        $row_class = 'silver';
                    } elseif ($position == 3) {
                        $row_class = 'bronze';
                    }

                    echo "<tr class='" . $row_class . "'>";
                    echo "<td style='padding: 10px; border: 1px solid #ccc; text-align: center;'>" . $position++ . "</td>";
                    echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars($player['pseudo']) . "</td>";
                    echo "<td style='padding: 10px; border: 1px solid #ccc; text-align: center;'>" . $player['score'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
