<?php

require("../libs/controllers/database.php");
include("../libs/PHPMarkdown/Parsedown.php");

$articles = $bdd->query("SELECT * FROM NEWS WHERE SERVICE='DOMCORD' ORDER BY DATE DESC LIMIT 1");
$articles = $articles->fetch();

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

echo html_entity_decode($parsedown->text($articles["CONTENU"]));
