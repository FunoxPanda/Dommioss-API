<?php

// Différents types: success, info, warning, danger
// La base de donnée doit avoir aupréalable été importée

function createNotif($userid, $type, $content)
{

    if (!isset($bdd)) {
        if (file_exists("../api/libs/controllers/database.php")) include("../api/libs/controllers/database.php");
        if (file_exists("../../api/libs/controllers/database.php")) include("../../api/libs/controllers/database.php");
        if (file_exists("../../libs/controllers/database.php")) include("../../libs/controllers/database.php");
    }

    if (!isset($bdd)) return false;
    if ($type !== 'success' && $type !== 'info' && $type !== 'warning' && $type !== 'danger') return false;

    $SQL = $bdd->prepare("INSERT INTO `NOTIFICATIONS`(`DATE`, `USER_ID`, `CONTENT`, `TYPE`, `STATUS`) VALUES (NOW(),?,?,?,0)");
    $SQL->execute(array($userid, $content, $type));

    return true;
}
