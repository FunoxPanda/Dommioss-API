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
const IP = "eclipsia.fr";
const Port = "25800";

$onlinePlayersAmount = file_get_contents('https://minecraft-api.com/api/ping/online/' . IP . '/' . Port);
$status = file_get_contents('https://minecraft-api.com/api/ping/status/' . IP . '/' . Port);
$ping = file_get_contents('https://minecraft-api.com/api/ping/response/' . IP . '/' . Port);
$version = file_get_contents('https://minecraft-api.com/api/ping/version/' . IP . '/' . Port);


$response = array();
if ($status !== "Hors Ligne") {
    $response = [
        "code" => 200,
        "status" => $status,
        "ping" => $ping,
        "version" => $version,
        "onlineAmount" => $onlinePlayersAmount
    ];
} else {
    $response = [
        "code" => 200,
        "status" => $status,
        "ping" => 0
    ];
}

if (isset($response["code"])) http_response_code($response["code"]) || http_response_code(500);
echo json_encode($response);
