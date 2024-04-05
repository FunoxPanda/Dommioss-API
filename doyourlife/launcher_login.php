<?php

require('../libs/controllers/database.php');

header('Content-Type: application/json');

function requestEnd($code, $data)
{
    http_response_code($code);
    if (gettype($data) === 'string')
        echo json_encode(['code' => $code, 'message' => $data]);
    else if (gettype($data) === 'array')
        echo json_encode(array_merge(['code' => $code], $data));
    exit();
}

function genRandomString($nbChar)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 1, $nbChar);
}

function genNames()
{

    function getFullName()
    {
        $data = file_get_contents('https://api.namefake.com/french-france/random/');
        $message = json_decode($data, true);
        return $message['name'];
    }

    $fullName = getFullName();
    while (strlen($fullName) > 16 || substr_count($fullName, ' ') > 1)
        $fullName = getFullName();

    return explode(' ', $fullName);
}

$message = file_get_contents('php://input');
$message = json_decode($message, true);

if (!isset($message['email']) || empty($message['email']))
    requestEnd(400, 'Missing email');

if (isset($message['password']) && !empty($message['password'])) {

    $user = $bdd->prepare('SELECT * FROM MEMBRES WHERE MAIL=?');
    $user->execute([$message['email']]);

    if ($user->rowCount() !== 1)
        requestEnd(401, 'Invalid credentials');

    $user = $user->fetch();

    if (!password_verify($message['password'], $user['PASSWORD']))
        requestEnd(401, 'Invalid credentials');
} elseif (isset($message['refreshToken']) && !empty($message['refreshToken'])) {

    $user = $bdd->prepare('SELECT MEMBRES.*, TOKEN FROM DOYOURLIFE_TOKENS INNER JOIN MEMBRES ON DOYOURLIFE_TOKENS.USER_ID=MEMBRES.id && TOKEN=?');
    $user->execute([$message['refreshToken']]);

    if ($user->rowCount() !== 1)
        requestEnd(401, 'Invalid refreshToken');

    $user = $user->fetch();

    if ($user['TOKEN'] !== $message['refreshToken'])
        requestEnd(401, 'Invalid refreshToken');

    $deleteToken = $bdd->prepare('DELETE FROM DOYOURLIFE_TOKENS WHERE TOKEN=?');
    $deleteToken->execute([$message['refreshToken']]);
} else
    requestEnd(400, 'Missing password or refreshToken');

if ($user['STATUT'] === -1)
    requestEnd(403, 'Account disabled');

if ($user['STATUT'] === 0)
    requestEnd(403, 'Account not verified');

if ($user['STATUT'] === 2)
    requestEnd(403, 'Account suspended');

$dylUser = $bdd->prepare('SELECT * FROM DOYOURLIFE_MEMBERS WHERE MEMBER_ID = :id');
$dylUser->execute(array('id' => $user['id']));
$dylUser = $dylUser->fetch();

$updateIp = $bdd->prepare('UPDATE MEMBRES SET DYL_IP=? WHERE id=?');
$updateIp->execute([$_SERVER['REMOTE_ADDR'], $user['id']]);

$newToken = genRandomString(50);
$insertToken = $bdd->prepare('INSERT INTO DOYOURLIFE_TOKENS VALUES (?, ?)');
$insertToken->execute([$user['id'], $newToken]);

if (empty($user['PRENOM']) || empty($user['NOM'])) {
    $names = genNames();
    $user['PRENOM'] = $names[0];
    $user['NOM'] = $names[1];
    $updateNames = $bdd->prepare('UPDATE MEMBRES SET PRENOM=?, NOM=? WHERE id=?');
    $updateNames->execute([$names[0], $names[1], $user['id']]);
}

if (isset($user['id'])) {

    $skinUrl = $bdd->prepare('SELECT * FROM DOYOURLIFE_SKINS WHERE SKIN_ID = :memberId');
    $skinUrl->execute(array('memberId' => $dylUser['SKIN_ID']));
    $skinUrl = $skinUrl->fetch();


    $skinUrl = json_decode(base64_decode($skinUrl['SKIN_VALUE']));
    $skinUrl = $skinUrl->textures->SKIN->url;
}

requestEnd(200,  [
    'pseudonyme' => $user['PSEUDONYME'],
    'firstName' => $user['PRENOM'],
    'lastName' => $user['NOM'],
    'rank' => $user['GRADE'],
    'skinUrl' => $skinUrl,
    'refreshToken' => $newToken
]);
