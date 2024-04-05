<?php

require('../libs/controllers/database.php');
$limit = 100; // Définition par défaut du statut isList
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
if(isset($_GET['limit']) and !empty($_GET['limit'])) $limit = htmlspecialchars($_GET['limit']);

if ($limit) {

    $SQL = $bdd->query('SELECT * FROM NEWS WHERE SERVICE = "DYL3" ORDER BY id DESC LIMIT '.htmlspecialchars($limit));

    if ($SQL->rowCount() > 0) {
        $articles = [];
        while ($article = $SQL->fetch()) {
            array_push($articles, [
                "id" => $article["id"],
                "title" => $article["TITRE"],
                "author" => $article["AUTEUR"],
                "content" => $article["CONTENU"],
                "date_post" => $article["DATE"]
            ]);
        }
        $response = [
            "code" => 200,
            "articles" => $articles
        ];
    } else {

        $response = ['code' => 404, 'message' => 'List is empty'];
    }

} else {
    $response = ['code' => 401, 'message' => 'Bad request'];
}

// Retour client

if (isset($response["code"])) http_response_code($response["code"]) || http_response_code(500);
echo json_encode($response);
