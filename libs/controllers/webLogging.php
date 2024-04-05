<?php

// Différents types: add, update, delete, lock, other
// La base de donnée doit avoir aupréalable été importée

function createLog($type, $content)
{

    if(file_exists("../api/libs/controllers/database.php")) include("../api/libs/controllers/database.php");
    if(file_exists("../../api/libs/controllers/database.php")) include("../../api/libs/controllers/database.php");
    if(file_exists("../../libs/controllers/database.php")) include("../../libs/controllers/database.php");
    
    if (!isset($bdd)) return false;
    if ($type !== 'add' && $type !== 'update' && $type !== 'delete' && $type !== 'lock' && $type !== 'other') return false;

    $SQL = $bdd->prepare("INSERT INTO `LOGS_WEBSITE`(`DATE_CREATION`, `TYPE`, `CONTENU`) VALUES (NOW(),?,?)");
    $SQL->execute(array($type, $content));

    return true;
}
