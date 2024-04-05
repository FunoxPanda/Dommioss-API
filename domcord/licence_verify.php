<?php

require("../../database.php");

$key = htmlspecialchars($_GET["key"]);
$domain = str_replace("www.", "", htmlspecialchars($_GET["domain"]));

if (isset($key) && strlen($key) === 60) {

    if (isset($domain) && !empty($domain) && str_contains($domain, ".")) {

        /*$rows = $bdd->prepare("SELECT * FROM DOMCORD_LICENSE WHERE DOMAIN=? && STATUS=0 && LICENSE_KEY=?");
        $rows->execute([$domain, $key]);
        $rowCount = $rows->rowCount();

        if ($rowCount === 0) {
            $reply = ["code" => 404, "message" => "License does not exist"];
        } elseif ($rowCount === 1) {
            $row = $rows->fetch();
            $reply = ["code" => 200, "status" => intval($row["STATUS"]), "type" => $row["TYPE"]];
        } else {
            $reply = ["code" => 500, "message" => "Error while counting amount of rows"];
        }*/

        $reply = ["code" => 200, "status" => 0, "type" => "Classic"];
    } else {
        $reply = ["code" => 400, "message" => "Unknown or invalid domain"];
    }
} else {
    $reply = ["code" => 400, "message" => "Unknown or invalid key"];
}

header("Content-type: application/json; charset=UTF-8");
http_response_code($reply["code"]);
echo json_encode($reply);
