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

// Type de retour:
//   | Succès: Header code
//   | Erreur: JSON string + Integer
header('Content-Type: application/json; charset=utf-8');

// Récupération des données
// Méthode: [POST]


// Traitement des données

$sentances = [
    "Made by DommiossGroup ft. Eclipsia",
    "Pensez à lire le règlement du Serveur",
    "DOYOURLIFE.DOMMIOSS.FR",
    "Les ours sont presque aussi beaux que les pandas",
    "#PrayForUkraine",
    "Build par FunoxPanda"
];


// Retour client

$response = [
    "code" => 200,
    "text" => $sentances[array_rand($sentances)]
];

header("Content-type: application/json; charset=UTF-8");
http_response_code($response["code"]);
echo json_encode($response);


