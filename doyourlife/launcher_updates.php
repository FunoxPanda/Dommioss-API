<?php

$response = [
    "version" => "1.0.0",
    "downloads" => [
        "windows" => "https://api.dommioss.fr/cdn/launcher/doyourlife-launcher-installer.exe"
    ],
    "code" => 200
];

header("Content-type: application/json; charset=UTF-8");
http_response_code($response["code"]);
echo json_encode($response);
