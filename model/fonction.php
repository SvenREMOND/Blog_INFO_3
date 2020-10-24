<?php

include('sql/connexion.php');

class Articles{

    static function Categories(){
        $pdo = connexion();

        $sql = 'SELECT * FROM catégories' ;

        $query = $pdo->prepare($sql);
        $query->execute();

        $result = [];

        while($array = $query->fetchObject('Articles')){
            array_push($result, $array);
        }


        return $result;
    }

    static function Read_Recent(){

        $pdo = connexion();

        $sql = 'SELECT * FROM articles INNER JOIN catégories ON articles.id_cat=catégories.id_cat INNER JOIN utilisateurs ON articles.id_user=utilisateurs.id_user ORDER BY id_article DESC' ;

        $query = $pdo->prepare($sql);
        $query->execute();

        $result = [];

        while($array = $query->fetchObject('Articles')){
            array_push($result, $array);
        }


        return $result;
    }

    static function Read_All_cat($cat){

        $pdo = connexion();

        $sql = 'SELECT * FROM articles INNER JOIN utilisateurs ON articles.id_user=utilisateurs.id_user WHERE id_cat = ? ORDER BY id_article DESC' ;

        $query = $pdo->prepare($sql);
        $query->execute([$cat]);

        $result = [];
        
        while($array = $query->fetchObject('Articles')){
            array_push($result, $array);
        }

        return $result;
    }

    static function Read_One($id){

        $pdo = connexion();

        $sql = 'SELECT * FROM articles INNER JOIN utilisateurs ON articles.id_user=utilisateurs.id_user WHERE id_article = ?' ;

        $query = $pdo->prepare($sql);
        $query->execute([$id]);

        $result = $query->fetchObject('Articles');

        return $result;
    }

    static function Read_comments($id){

        $pdo = connexion();

        $sql = 'SELECT * FROM commentaires INNER JOIN utilisateurs ON commentaires.id_user=utilisateurs.id_user WHERE id_article = ?';

        $query = $pdo->prepare($sql);
        $query->execute([$id]);

        $comments = $query->fetchAll(PDO::FETCH_OBJ);

        $comments_by_id = [];

        foreach($comments as $comment){
            $comments_by_id[$comment->id_com] = $comment;
        }

        foreach($comments as $k =>$comment){
            if($comment->id_com_parent != 0){
                $comments_by_id[$comment->id_com_parent]->children[] = $comment;
                unset($comments[$k]);
            }
        }

        return $comments;
    }

    static function Supp($id){

        $pdo = connexion();

        $sql = 'DELETE FROM `articles` WHERE `articles`.`id_article` = ?' ;

        $query = $pdo->prepare($sql);
        $query->execute([$id]);

        header('Location: ./index.php');

    }
}

class Formulaire_Article{
    
    static function categories(){

        $pdo = connexion();

        $sql = 'SELECT * FROM `catégories` ' ;

        $query = $pdo->prepare($sql);
        $query->execute();

        $result = $query->fetchAll();

        return $result;
    }

    static function ajout($tab){

        $pdo = connexion();

        if(isset($tab['Poster'])){

            $titre = htmlspecialchars($tab['titre']);
            $contenue = $tab['contenu'];
            $cat = $tab['categorie'];
        
            if(!empty($contenue) && !empty($cat) && !empty($titre)){
        
                $ajout = $pdo->prepare('INSERT INTO `articles` (`titre_article`, `contenu_article`, `date_article`, `id_user`, `id_cat`) VALUES (?, ?,NOW(), ?, ?)');
                $ajout->execute([$titre, $contenue, $_SESSION['id_user'], $cat]);
        
                header('Location: ./index.php?cat=NewArticles');
            }else{
                echo '<script>alert("Les champs ne sont pas remplie !"); window.location.href="./index.php?cat=ajout"</script>';
            }
        }
    }

    static function Update($tab, $id){
        $pdo = connexion();

        if(isset($tab['Poster'])){

            $titre = htmlspecialchars($tab['titre']);
            $contenue = $tab['contenu'];
            $cat = $tab['categorie'];
        
            if(!empty($contenue) && !empty($cat) && !empty($titre)){
        
                $ajout = $pdo->prepare("UPDATE `articles` SET `titre_article` = ?, `contenu_article` = ?, `id_cat` = ?  WHERE `articles`.`id_article` = ?");
                $ajout->execute([$titre, $contenue, $cat, $id]);
        
                header('Location: ./index.php');
            }else{
                echo '<script>alert("Les champs ne sont pas remplie !"); window.location.href="./index.php?cat=ajout"</script>';
            }
        }
    }

    static function Comment($info, $id){

        $pdo = connexion();

        if(isset($info['Commenter'])){

            $contenue = $info['comment_content'];
            $id_article = $id;
            if(empty($info['id_parent'])){
                $id_parent = null;
            }else{
                $id_parent = $info['id_parent'];
            }
        
            if(!empty($contenue)){
        
                $ajout = $pdo->prepare('INSERT INTO `commentaires` (`contenu_com`, `date_com`, `id_article`, `id_com_parent`, `id_user`) VALUES (?, NOW(), ?, ?, ?)');
                $ajout->execute([$contenue, $id_article, $id_parent, $_SESSION['id_user']]);
        
                header('Location: ./index.php?action=1article&id='.$id_article.'');
            }else{
                echo '<script>alert("Les champs ne sont pas remplie !"); window.location.href="./index.php?action=1article&id='.$id_article.'</script>';
            }
        }
    }
}

class Formulaire_User{

    static function LogIn($log){

        $bdd = connexion();

        $pseudoconnect = $mdpconnect = "";
        $nom = $prenom = $mail = $phone = $pass = $pass_confirm = "";


        if(isset($log['formconnexion'])) {
            $pseudoconnect = $log['pseudo'];
            $mdpconnect = $log['mdpconnect'];
            if(!empty($pseudoconnect) && !empty($mdpconnect)) {
            
                $requser = $bdd->prepare("SELECT * FROM `utilisateurs` WHERE `utilisateurs`.`pseudo_user` = ? ");
            
                $requser->execute(array($pseudoconnect));
            
                $userinfo = $requser->fetch();
            
                if(password_verify($mdpconnect, $userinfo['mdp_user'])) {
                
                    $_SESSION['co'] = true; 
                
                    $_SESSION['nom'] = $userinfo['nom_user'].' '.$userinfo['prenom_user'];
                
                    $_SESSION['pseudo'] = $userinfo['pseudo_user'];
                
                    $_SESSION['id_user'] = $userinfo['id_user'];
                
                    header('Location: ./index.php');
                
                }else{
                    echo '<script>alert("Le pseudo ou le mot de passe est incorect !"); window.location.href="./index.php?action=Log"</script>';
                }
            }
        }
    }

    static function SignIn($log){

        $bdd = connexion();

        if(isset($log['inscription'])) {
            $nom = htmlspecialchars($log['nom']);
            $prenom = htmlspecialchars($log['prenom']);
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $pass = $log['mdp'];
            $pass_confirm = $log['mdp_confirm'];

            $verif_pseudo = $bdd->prepare("SELECT pseudo_user FROM `utilisateurs` ");
                
            $verif_pseudo->execute();

            $verif_pseudo = $verif_pseudo->fetch();

            if(in_array($pseudo, $verif_pseudo)){
                echo '<script>alert("Le pseudo existe déjà"); window.location.href="./index.php?action=Log"</script>';
            }else{
                if(!empty($pass) && !empty($pass_confirm) && !empty($nom) && !empty($prenom) && !empty($pseudo)) {
            
                    if($pass == $pass_confirm){
                    
                        $mdp = password_hash($pass, PASSWORD_DEFAULT);
                    
                        $inscription = $bdd->prepare("INSERT INTO `utilisateurs` (`nom_user`, `prenom_user`, `pseudo_user`, `mdp_user`) VALUES (?, ?, ?, ?) ");
                    
                        $inscription->execute(array($nom, $prenom, $pseudo, $mdp));
                    
                    
                        $newid = $bdd->lastInsertId();
                        
                        $_SESSION['co'] = true; 
                        
                        $_SESSION['nom'] = $nom.' '.$prenom;
    
                        $_SESSION['pseudo'] = $pseudo;
                    
                        $_SESSION['id_user'] = $newid;
                    
                        header('Location: ./index.php');
                    
                    }else{
                        echo '<script>alert("Les mot de passe ne sont pas identique !"); window.location.href="./index.php?cat=LogInSignIn"</script>';
                    }
                }else{
                    echo '<script>alert("Tout les champ ne sont pas remplies !"); window.location.href="./index.php?cat=LogInSignIn"</script>';
                }
            }

            
        }

    }
     
    static function LogOut(){
        
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();


        header('Location: ./index.php');
    }
}