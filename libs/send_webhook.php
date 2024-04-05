<?php

function sendwebhook($type, $title, $messageContent, $hexColor)
{
    $webhookurl = "https://discord.com/api/webhooks/919553180558303232/Nvt_d7CItVDYeTcwo9XvsHsohr4_mLYPLSezMt-6FF0cC0vBEBtQZs5-chseoEyHCfWW";
    
    if (isset($type) && $type == "rankBought") {
        $webhookurl = 'https://discord.com/api/webhooks/1012289885706670140/nkO-UTHL9WMx7pu8XZabn7YDc1LQZEJbHP-ombinEmcuoJyMqN7WorSnymjyvkU1WGlR';
    }
    
    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([


        "username" => "Groupe Dommioss - Intéractions",
        "avatar_url" => "https://dommioss.fr/assets/images/dommioss.png",
        "tts" => false,

        "embeds" => [
            [
                "title" => $title,
                "type" => "rich",
                "description" => $messageContent,
                "url" => "https://dommioss.fr/",
                "timestamp" => $timestamp,
                "color" => hexdec($hexColor),
            ]
        ]

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
    // echo $response;
    curl_close($ch);
}
