<?php

require('../libs/controllers/database.php');
$isList = false; // Définition par défaut du statut isList
/*
╭━━━┳━━━┳━╮╭━┳━╮╭━┳━━┳━━━┳━━━┳━━━╮
╰╮╭╮┃╭━╮┃┃╰╯┃┃┃╰╯┃┣┫┣┫╭━╮┃╭━╮┃╭━╮┃
╱┃┃┃┃┃╱┃┃╭╮╭╮┃╭╮╭╮┃┃┃┃┃╱┃┃╰━━┫╰━━╮
╱┃┃┃┃┃╱┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃╱┃┣━━╮┣━━╮┃
╭╯╰╯┃╰━╯┃┃┃┃┃┃┃┃┃┃┣┫┣┫╰━╯┃╰━╯┃╰━╯┃
╰━━━┻━━━┻╯╰╯╰┻╯╰╯╰┻━━┻━━━┻━━━┻━━━╯ 

//
*/

// Requête de tous les articles d'une catégorie ==> ?isList=true&category=[TEXT]
// ex: https://api.dommioss.fr/doyourlife/get_wiki.php?isList=true&category=example

// Type de retour:
//   | Succès: Header code
//   | Erreur: JSON string + Integer
header('Content-Type: application/json; charset=utf-8');

// Récupération des données
// Méthode: [GET]

if (isset($_GET['isList']) and $_GET['isList'] == "true" and isset($_GET['category']) and !empty($_GET['category'])) {
    $isList = true;

    $SQL = $bdd->prepare('SELECT id, TITLE, CATEGORY, PUBLICATION_DATE FROM DOYOURLIFE_WIKI WHERE CATEGORY = ?');
    $SQL->execute(array(htmlspecialchars($_GET['category'])));

    if ($SQL->rowCount() > 0) {
        $articles = [];
        while ($article = $SQL->fetch()) {
            array_push($articles, [
                "id" => $article["id"],
                "title" => $article["TITLE"]
            ]);
        }
        $response = [
            "code" => 200,
            "articles" => $articles
        ];
    } else {

        $response = ['code' => 404, 'message' => 'Category is empty or does not exist'];
    }
} else if (isset($_GET['getArticleById']) and !empty($_GET['getArticleById'])) {

    $articleId = htmlspecialchars($_GET['getArticleById']);

    $SQL = $bdd->prepare('SELECT * FROM DOYOURLIFE_WIKI WHERE id = ?');
    $SQL->execute(array($articleId));
    if ($SQL->rowCount() > 0) {
        $SQL = $SQL->fetch();
        $response = array();
        $response = [
            "code" => 200,
            "title" => $SQL['TITLE'],
            "category" => $SQL['CATEGORY'],
            "publication_date" => $SQL['PUBLICATION_DATE'],
            "content" => $SQL['CONTENT'],
            "edition_date" => $SQL['EDITION_DATE'],
            "author" => $SQL['AUTHOR_ID']
        ];
    } else {
        $response = ['code' => 404, 'message' => 'Article not found'];
    }
} else {
    $response = ['code' => 401, 'message' => 'Bad request'];
}

// Retour client

if (isset($response["code"])) http_response_code($response["code"]) || http_response_code(500);
echo json_encode($response);
