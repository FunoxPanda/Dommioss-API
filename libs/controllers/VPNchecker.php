<?php

getBooleanFAI($_GET['ip']);

function getBooleanFAI($ip)
{
    $host = @gethostbyaddr($ip);
    $fai = false;
    if (substr_count($host, 'proxad')) $fai = true;
    if (substr_count($host, 'orange')) $fai = true;
    if (substr_count($host, 'wanadoo')) $fai = true;
    if (substr_count($host, 'sfr')) $fai = true;
    if (substr_count($host, 'club-internet')) $fai = true;
    if (substr_count($host, 'neuf')) $fai = true;
    if (substr_count($host, 'gaoland')) $fai = true;
    if (substr_count($host, 'bbox')) $fai = true;
    if (substr_count($host, 'bouyg')) $fai = true;
    if (substr_count($host, 'numericable')) $fai = true;
    if (substr_count($host, 'tele2')) $fai = true;
    if (substr_count($host, 'videotron')) $fai = true;
    if (substr_count($host, 'belgacom')) $fai = true;
    if (substr_count($host, 'bell.ca')) $fai = true;
    return $fai;
}


// ignore the French Internet Providers
if (getBooleanFAI($_GET['ip']) == false) {

    $ip = gethostbyname($_GET['ip']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, 'http://v2.api.iphub.info/ip/' . $ip);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Key: MTc0MDY6eVhIMEpRbGhiWG1mbHJnT01ESzhaMU5jdnlzZzU2V1E=')); // Obtenez votre clé en vous inscrivant sur https://iphub.info
    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result, true);

    $isp = $obj['isp'];

    if ($obj['block'] == '0') {
        $block = "IP résidentielle / non classifiée (c'est-à-dire Safe IP)";
    } else if ($obj['block'] == '1') {
        $block = "Détection d'un VPN ou PROXY ou VPS ou Serveur dédié ou hébergeur hosting...";
    } else if ($obj['block'] == '2') {
        $block = "IP non résidentielle et résidentielle (avertissement, peut flagrant des personnes innocentes)";
    } else {
        $block = "Connexion inconnu";
    }

    echo $block;
}
