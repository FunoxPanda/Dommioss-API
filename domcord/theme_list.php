<?php

header("Content-type: application/json; charset=UTF-8");

require("../libs/controllers/database.php");

if (isset($_GET["key"])) {

    $domain = str_replace("www.", "", htmlspecialchars($_GET["domain"]));

    $check = $bdd->prepare("SELECT * FROM DOMCORD_LICENSE WHERE LICENSE_KEY = ? AND DOMAIN = ? AND STATUS = 0");
    $check->execute(array(htmlspecialchars($_GET["key"]), $domain));

    if ($check->rowCount() > 0) {

        if (isset($_GET["id"])) {
            $SQL = $bdd->prepare("SELECT * FROM DOMCORD_THEMES WHERE id = ?");
            $SQL->execute(array(htmlspecialchars($_GET["id"])));
            $rows = array();
            while ($r = $SQL->fetch()) {
                $rows[] = $r;
            }
            print json_encode($rows);
        } else {
            $SQL = $bdd->query("SELECT * FROM DOMCORD_THEMES");
            $rows = array();
            while ($r = $SQL->fetch()) {
                $rows[] = $r;
            }
            print json_encode($rows);
        }
    }
}
