<?php

/*
╭━━━┳━━━┳━╮╭━┳━╮╭━┳━━┳━━━┳━━━┳━━━╮
╰╮╭╮┃╭━╮┃┃╰╯┃┃┃╰╯┃┣┫┣┫╭━╮┃╭━╮┃╭━╮┃
╱┃┃┃┃┃╱┃┃╭╮╭╮┃╭╮╭╮┃┃┃┃┃╱┃┃╰━━┫╰━━╮
╱┃┃┃┃┃╱┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃┃╱┃┣━━╮┣━━╮┃
╭╯╰╯┃╰━╯┃┃┃┃┃┃┃┃┃┃┣┫┣┫╰━╯┃╰━╯┃╰━╯┃
╰━━━┻━━━┻╯╰╯╰┻╯╰╯╰┻━━┻━━━┻━━━┻━━━╯ 

// 
*/

// Introduction de la base de donnée
require('../../database.php');

// Type de retour:
//   | Succès: Header code
//   | Erreur: JSON string + Integer

header('Content-Type: application/json; charset=utf-8');

// Récupération des données
// Méthode: [BODY]

$json = file_get_contents('php://input');
$data = json_decode($json);
unset($json);

// Traitement des données

$id = htmlspecialchars($data->userid);

if (isset($id) and !empty($id)) {
    $SQL = $bdd->prepare('SELECT * FROM `DOYOURLIFE_LOGS` WHERE `MEMBER_ID` = ?');
    $SQL->execute(array($id));


    if ($SQL->rowCount() > 0) {
        $jsonLogs = array();
        while($r = $SQL->fetch()){
            $jsonLogs[] = $r;
        }
        http_response_code(200);
        echo json_encode($jsonLogs);
    } else {

        http_response_code(404);
        $reply = ['code' => 404, 'message' => 'Logs are empty'];
        echo json_encode($reply);
    }
} else {
    http_response_code(400);
    $reply = ['code' => 400, 'message' => 'Bad request'];
    echo json_encode($reply);
}
// Retour client
