<?php

require('../libs/controllers/database.php');

/*
╭━━━┳━━━┳━╮╭━┳━╮╭━┳━━┳━━━┳━━━┳━━━╮
╰╮╭╮┃╭━╮┃┃╰╯┃┃┃╰╯┃┣┫┣┫╭━╮┃╭━╮┃╭━╮┃
╱┃┃┃┃┃╱┃┃╭╮╭╮┃╭╮╭╮┃┃┃┃┃╱┃┃╰━━┫╰━━╮
╱┃┃┃┃┃╱┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃╱┃┣━━╮┣━━╮┃
╭╯╰╯┃╰━╯┃┃┃┃┃┃┃┃┃┃┣┫┣┫╰━╯┃╰━╯┃╰━╯┃
╰━━━┻━━━┻╯╰╯╰┻╯╰╯╰┻━━┻━━━┻━━━┻━━━╯ 

// 
*/

// Type de retour:
//   | Succès: Header code
//   | Erreur: JSON string + Integer
header('Content-Type: application/json; charset=utf-8');

// Récupération des données
// Méthode: [GET]

if(isset($_GET['userAccountId']) and !empty($_GET['userAccountId'])){

    $userAccountId = htmlspecialchars($_GET['userAccountId']);

    $SQL = $bdd->prepare('SELECT id, MAIL, PSEUDONYME, PRENOM, NOM, STATUT, GRADE FROM MEMBRES WHERE id = ?');
    $SQL->execute(array($userAccountId));
    $userinfo = $SQL->fetch();
}

// Traitement des données

if ($userAccountId) {
    $response = [
        "code" => 200,
        "userAccountId" => $userAccountId,
        "mail" => $userinfo['MAIL'],
        "pseudonyme" => $userinfo['PSEUDONYME'],
        "prenom" => $userinfo['PRENOM'],
        "nom" => $userinfo['NOM'],
        "statut" => intval($userinfo['STATUT']),
        "grade" => $userinfo['GRADE']
    ];
}

// Retour client

http_response_code($response["code"]);
echo json_encode($response);



