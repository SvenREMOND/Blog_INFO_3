<?php
session_start();

include('./include/twig.php');
include('./model/fonction.php');
$twig = init_twig();


if(isset($_GET['action'])){
    $action = $_GET['action'];
}else{
    $action = "Index";
}
if(isset($_GET['id'])){
    $id = $_GET['id'];
}else{
    $id = "";
}
if(isset($_GET['type'])){
    $type = $_GET['type'];
}else{
    $type = "";
}


switch ($action) {

    case "Index" :
        $c = Articles::Categories();
        echo $twig->render('index.html', [
            'session' => $_SESSION ,
            'categorie' => $c
        ]);
    break;

    case "NewArticles" :
        $a = Articles::Read_Recent();
        echo $twig->render('newarticle.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "form" :

        switch ($type) {

            case "ajout" :
                Formulaire_Article::ajout($_POST);
            break;

            case "supp" :
                Articles::Supp($_GET['id']);
            break;

            case "update" :
                    Formulaire_Article::Update($_POST, $id);
            break;

            case "com" :
                Formulaire_Article::Comment($_POST, $id);
            break;

            default :
                $c = Formulaire_Article::categories();
                echo $twig->render('formulaire.html', [
                    'session' => $_SESSION ,
                    'Liste' => $c
                ]);
            break;

        }

    break;

    case "update":
        $a = Articles::Read_One($id);
        $c = Formulaire_Article::categories();
        echo $twig->render('formulaire.html', [
            'session' => $_SESSION ,
            'article' => $a ,
            'Liste' => $c
        ]);
    break;

    case "html" : 
        $a = Articles::Read_All_cat(1);
        echo $twig->render('articles.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "css" : 
        $a = Articles::Read_All_cat(2);
        echo $twig->render('articles.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "js" : 
        $a = Articles::Read_All_cat(3);
        echo $twig->render('articles.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "php" : 
        $a = Articles::Read_All_cat(4);
        echo $twig->render('articles.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "sql" : 
        $a = Articles::Read_All_cat(5);
        echo $twig->render('articles.html', [
            'session' => $_SESSION ,
            'articles' => $a
        ]);
    break;

    case "1article" :
        $a = Articles::Read_One($id);
        $c = Articles::Read_comments($id);
        echo $twig->render('1article.html', [
            'session' => $_SESSION ,
            'article' => $a ,
            'commentaires' => $c
        ]);
    break;

    case "Log":
        if(isset($_GET['type'])){
            $type = $_GET['type'];

            switch ($type) {

                case "LogIn" :
                    Formulaire_User::LogIn($_POST);
                break;

                case "SignIn" :
                    Formulaire_User::SignIn($_POST);
                break;

                case "Out" :
                    Formulaire_User::LogOut();
                break;

            }

        }else{
            echo $twig->render('Log.html', [
            ]);
        }

    break;
}