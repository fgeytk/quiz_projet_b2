<?php
        /*
        Configuration pour Docker
        */
        $servername = 'db'; // Nom du service Docker
        $user = 'quiz';
        $password = 'quiz';
        $database = 'quiz';
        $port = NULL;
        //Utlisation de MySQLI proédural
        try{
            $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $user, $password);
            // Définir le mode d'erreur PDO sur Exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connexion réussie !";
            }
            /*On capture les exceptions si une exception est lancée et on affiche
            les informations relatives à celle-ci*/
            catch(PDOException $e){
            echo "Erreur : " . $e->getMessage();
            }
?>