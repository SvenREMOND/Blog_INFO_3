<?php

    /*  
        Pour les autre fichier :
        <?php
        include('./include/connexion.php');

        $pdo = connexion();

        $sql = 'SELECT * FROM auteurs';

        $query = $pdo->prepare($sql);
        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

 
        $pdo = null 
    */
    
    // on inclut le fichier de configuration
    require('config.php');

    function connexion() {
        // indique qu'on utilise la variable globale $config, définie dans le fichier 'config.php'
        // (sinon $config serait une variable locale uniquement définie dans cette fonction et donc vide)
        global $config;
        // connexion en utilisant les informations stockées dans la variable $config
        $pdo = new PDO($config['driver'].':host='.$config['serveur'].';dbname='.$config['base'].';charset=utf8',$config['utilisateur'], $config['mdp']);

        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
 
 
        // test de la variable PDO et renvoi de sa valeur
        if ($pdo) {
            // retourne l'identifiant de connexion
            return $pdo;
        }
        else {
            echo '<p>Erreur de connexion</p>';
            // stoppe les scripts PHP
            exit;
        }
    }

?>
