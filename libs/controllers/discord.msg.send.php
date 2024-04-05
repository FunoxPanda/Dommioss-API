<?php
function security_webhookmessage($message)
{
    $webhookurl = "DISCORD_URL_WEBHOOK";

    $json_data = json_encode([
        "content" => "<@&655127344977281025>",
        "username" => "DommiossSecurity",
        "avatar_url" => "https://dommioss.fr/assets/images/dommioss.png",
        "tts" => false,
        "embeds" => [
            [
                "type" => "rich",
                "description" => $message,
                "timestamp" => date("c", strtotime("now")),
                "color" => hexdec("3366ff")
            ]
        ]

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_exec($ch);
    curl_close($ch);
}
