<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../api/libs/PHPMailer/src/Exception.php";
require "../api/libs/PHPMailer/src/PHPMailer.php";
require "../api/libs/PHPMailer/src/SMTP.php";

function sendMail($target, $subject, $htmlMessage)
{
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = "EMAIL_HOST";
    $mail->Port = "EMAIL_PORT";
    $mail->SMTPSecure = "ssl";
    $mail->SMTPAuth = true;
    $mail->Username = "EMAIL_USER";
    $mail->Password = "EMAIL_PASSWORD";

    $mail->setFrom("no-reply@dommioss.fr", "Groupe Dommioss");
    $mail->addAddress($target);

    $mail->CharSet = "UTF-8";
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlMessage;

    $mail->send();
}
