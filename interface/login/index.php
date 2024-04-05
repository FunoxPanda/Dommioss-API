<?php

if (isset($_GET['redirectUrl']) && !empty($_GET['redirectUrl'])) {
    $redirectUrl = $_GET['redirectUrl'];
} else {
    $redirectUrl = "https://dommioss.fr";
}
include('../../libs/controllers/database.php');
include('../../libs/controllers/function_detectOS.php');
include('../../libs/controllers/function_genCode.php');
include('../../libs/GoogleA2F/vendor/autoload.php');
include("../../libs/controllers/webLogging.php");

use OTPHP\TOTP;

if (isset($_POST['connexion'])) {

    if (isset($_POST['identifiant'])) {
        $mailconnexion = htmlspecialchars($_POST['identifiant']);
    }
    if (isset($_POST['motdepasse'])) {
        $passwordconnexion = htmlspecialchars($_POST['motdepasse']);
    }

    if (!empty($mailconnexion) && !empty($passwordconnexion)) {

        $SQL = $bdd->prepare("SELECT * FROM MEMBRES WHERE MAIL = ?");
        $SQL->execute(array($mailconnexion));

        $user_exist = $SQL->rowCount();

        if ($user_exist == 1) {

            $userinfo = $SQL->fetch();

            if (password_verify($passwordconnexion, $userinfo['PASSWORD'])) {

                if ($userinfo['STATUT'] < 2) {
                    if ($userinfo['STATUT'] > 0) {
                        if ($userinfo['2FA'] == 1 and empty($_POST['a2f_code'])) {

                            $error = 7;
                        } else if ($userinfo['2FA'] == 1 and !empty($_POST['a2f_code'])) {

                            $otp = TOTP::create($userinfo['2FA_KEY']);

                            if ($otp->verify(htmlspecialchars($_POST['a2f_code']))) {
                                $_SESSION['MAIL'] = $userinfo['MAIL'];
                                $_SESSION['id'] = $userinfo['id'];

                                $refreshToken = KeyGenerator(40);
                                $addToken = $bdd->prepare("INSERT INTO REFRESH_TOKENS (USER_ID, TOKEN) VALUES (?, ?)");
                                $addToken->execute(array($userinfo["id"], $refreshToken));

                                $error = 5;
                                createLog('lock', "<b>".$mailconnexion."</b> (".$_SERVER['REMOTE_ADDR'].") s'est connecté à au compte <b>".$userinfo['id']."</b> (".detect_os().")");
                            } else {
                                $error = 8;
                            }
                        } else if ($userinfo['2FA'] !== 1) {

                            $_SESSION['MAIL'] = $userinfo['MAIL'];
                            $_SESSION['id'] = $userinfo['id'];

                            $refreshToken = KeyGenerator(40);
                            $addToken = $bdd->prepare("INSERT INTO REFRESH_TOKENS (USER_ID, TOKEN) VALUES (?, ?)");
                            $addToken->execute(array($userinfo["id"], $refreshToken));

                            $error = 5;
                            createLog('lock', "<b>".$mailconnexion."</b> (".$_SERVER['REMOTE_ADDR'].") s'est connecté à au compte <b>".$userinfo['id']."</b> (".detect_os().")");
                        }
                    } else {
                        if ($userinfo['STATUT'] == 0) {
                            $error = 6;
                        } else {
                            $error = 9;
                        }
                    }
                } else {

                    $error = 4;
                }
            } else {

                $SQL3 = $bdd->prepare("SELECT * FROM MEMBRES WHERE MAIL = ? AND PASSWORD = ?");
                $SQL3->execute(array($mailconnexion, sha1($passwordconnexion)));
                $ouan = $SQL3->rowCount();
                if ($ouan > 0) {
                    if ($userinfo['STATUT'] > 0) {
                        $userinfo = $SQL3->fetch();
                        $_SESSION['MAIL'] = $userinfo['MAIL'];
                        $_SESSION['id'] = $userinfo['id'];

                        createLog('lock', "<b>".$mailconnexion."</b> (".$_SERVER['REMOTE_ADDR'].") s'est connecté à au compte <b>".$userinfo['id']."</b> (".detect_os().")");
                        $migrateaccount = $bdd->prepare("UPDATE `MEMBRES` SET `PASSWORD`= ? WHERE id = ?");
                        $migrateaccount->execute(array(password_hash($_POST['motdepasse'], PASSWORD_BCRYPT), $userinfo['id']));

                        $refreshToken = KeyGenerator(40);
                        $addToken = $bdd->prepare("INSERT INTO REFRESH_TOKENS (USER_ID, TOKEN) VALUES (?, ?)");
                        $addToken->execute(array($userinfo["id"], $refreshToken));

                        $error = 3;
                    } else {
                        if ($userinfo['STATUT'] == 0) {
                            $error = 6;
                        } else {
                            $error = 9;
                        }
                    }
                } else {
                    $error = 2;
                }
            }
        } else {

            $error = 2;
        }
    } else {

        $error = 1;
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter avec Dommioss</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
    <script src="https://kit.fontawesome.com/9effd4ff41.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

</head>

<body>
    <div class="form-container">
        <img src="assets/img/logo-dommioss-bleu.png" width="10%">
        <p class="title">mon compte dommioss</p>
        <form method="POST">
            <input required <?php if (isset($error) && $error == 7) {
                                echo 'type="hidden"';
                            } else {
                                echo 'type="text"';
                            } ?> placeholder="Adresse e-mail" name="identifiant" value="<?php if (isset($mailconnexion)) {
                                                                                            echo $mailconnexion;
                                                                                        } ?>">

            <input required <?php if (isset($error) && $error == 7) {
                                echo 'type="hidden"';
                            } else {
                                echo 'type="password"';
                            } ?> placeholder="Mot de passe" name="motdepasse" value="<?php if (isset($passwordconnexion)) {
                                                                                            echo $passwordconnexion;
                                                                                        } ?>">

            <?php if (isset($error) && $error == 7) { ?>
                <input placeholder="Code Google Authenticator" name="a2f_code">
            <?php } ?>

            <button class="btn btn-primary" type="submit" name="connexion"><i class="fas fa-key"></i> accéder à mon
                compte</button>
        </form>
        <div class="separator"></div>

        <p class="title">besoin d'aide ?</p>
        <a type="button" class="link" data-bs-toggle="modal" data-bs-target="#infoCompteDommiossModal">Qu'est ce qu'un
            compte Dommioss ?</a><br>
        <a class="link" href="https://dommioss.fr/reinitialiser-motdepasse" target="_blank">J'ai perdu mon mot de passe</a><br>
        <a class="link" href="https://dommioss.fr/inscription" target="_blank">Je n'ai pas de compte Dommioss</a>


        <!-- Modal -->
        <div class="modal fade" id="infoCompteDommiossModal" tabindex="-1" aria-labelledby="infoCompteDommiossModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="title">qu'est ce qu'un compte dommioss?</p>

                        <p>Un compte Dommioss vous permet d'accéder à de nombreux services proposés par le DommiossGroup
                            tels que sont DomCord ou DoYourLife.<br>Un compte Dommioss n'appartient qu'à celui qui l'a
                            créé, il en est par ailleurs le seul responsable.</brc>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "1500",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    var redirect = 'document.location.assign("<?php echo $redirectUrl . "?code=" . $refreshToken; ?>");';

    <?php if (isset($error)) { ?>
        <?php if (isset($error) && $error == 1) { ?>
            toastr.error("Veuillez saisir tous les champs", "Une erreur est survenue");
        <?php } else if (isset($error) && $error == 2) { ?>
            toastr.error("Les identifiants entrés ne sont pas valides", "Une erreur est survenue");
        <?php } else if (isset($error) && $error == 3) { ?>
            toastr.success("Votre compte a été migré pour plus de sécurité, vous allez être redirigé", "Connexion réussie");
            setTimeout(redirect, 1500);
        <?php } else if (isset($error) && $error == 4) { ?>
            toastr.error("Votre compte a été suspendu, vous ne pouvez pas y accéder", "Une erreur est survenue");
        <?php } else if (isset($error) && $error == 5) { ?>
            toastr.success("La connexion a été autorisée, vous allez être redirigé", "Connexion réussie");
            setTimeout(redirect, 1500);
        <?php } else if (isset($error) && $error == 6) { ?>
            toastr.error("Veuillez vous assurer que votre compte est vérifié. Cliquez sur le lien à droite du formulaire pour renvoyer le mail.", "Connexion refusée");

        <?php } else if (isset($error) && $error == 9) { ?>
            toastr.error("Ce compte a été désactivé par son/sa propriétaire. Contactez contact@dommioss.fr en cas d'erreur.", "Connexion refusée");

        <?php } else if (isset($error) && $error == 7) { ?>
            toastr.warning("La double authentification est activée sur votre compte. Veuillez spécifier le code Google Authenticator.", "Google Authenticator");

        <?php } else if (isset($error) && $error == 8) { ?>
            toastr.error("Le code que vous avez saisi est invalide.", "Google Authenticator");
        <?php } ?>
    <?php } ?>
</script>

</html>
