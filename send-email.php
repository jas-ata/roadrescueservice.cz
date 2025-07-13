<?php
header('Content-Type: application/json');

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader (pokud používáte Composer)
// require 'vendor/autoload.php';

// Nebo načtěte PHPMailer manuálně
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Konfigurace
$to_email = "info@roadrescueservice.cz";
$subject_prefix = "Webový formulář - ";

// SMTP konfigurace z integration souboru
$smtp_host = "mail3.zcom.cz";
$smtp_port = 465;
$smtp_username = "info@roadrescueservice.cz";
$smtp_password = "sps-dopravni";

// Kontrola, zda byl formulář odeslán
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Neplatná metoda požadavku.']);
    exit;
}

// Získání a sanitizace dat z formuláře
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
$subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

// Validace povinných polí
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Vyplňte prosím všechna povinná pole.']);
    exit;
}

// Validace emailu
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Zadejte prosím platnou emailovou adresu.']);
    exit;
}

try {
    // Vytvoření nové PHPMailer instance
    $mail = new PHPMailer(true);

    // SMTP konfigurace
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL/TLS
    $mail->Port = $smtp_port;
    $mail->CharSet = 'UTF-8';

    // Nastavení odesílatele a příjemce
    $mail->setFrom($smtp_username, 'Road Rescue Service - Web Form');
    $mail->addAddress($to_email);
    $mail->addReplyTo($email, $name);

    // Obsah emailu
    $email_subject = $subject_prefix . $subject;
    $email_body = "Nová zpráva z webového formuláře:\n\n";
    $email_body .= "Jméno: $name\n";
    $email_body .= "Email: $email\n";
    if (!empty($phone)) {
        $email_body .= "Telefon: $phone\n";
    }
    $email_body .= "Předmět: $subject\n\n";
    $email_body .= "Zpráva:\n$message\n";
    $email_body .= "\n---\n";
    $email_body .= "Odesláno: " . date('d.m.Y H:i:s') . "\n";
    $email_body .= "IP adresa: " . $_SERVER['REMOTE_ADDR'] . "\n";

    $mail->isHTML(false);
    $mail->Subject = $email_subject;
    $mail->Body = $email_body;

    // Odeslání hlavního emailu
    $mail->send();

    // Odeslání potvrzovacího emailu odesílateli
    try {
        $confirm_mail = new PHPMailer(true);
        
        $confirm_mail->isSMTP();
        $confirm_mail->Host = $smtp_host;
        $confirm_mail->SMTPAuth = true;
        $confirm_mail->Username = $smtp_username;
        $confirm_mail->Password = $smtp_password;
        $confirm_mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $confirm_mail->Port = $smtp_port;
        $confirm_mail->CharSet = 'UTF-8';

        $confirm_mail->setFrom($smtp_username, 'Road Rescue Service');
        $confirm_mail->addAddress($email, $name);

        $confirm_subject = "Potvrzení: " . $subject;
        $confirm_body = "Děkujeme za vaši zprávu!\n\n";
        $confirm_body .= "Toto je potvrzení, že jsme obdrželi vaši zprávu. Odpovíme vám co nejdříve.\n\n";
        $confirm_body .= "Vaše zpráva:\n";
        $confirm_body .= "---\n";
        $confirm_body .= "Předmět: $subject\n\n";
        $confirm_body .= "$message\n";
        $confirm_body .= "---\n\n";
        $confirm_body .= "S pozdravem,\n";
        $confirm_body .= "Road Rescue Service\n";
        $confirm_body .= "+420 737 998 496\n";
        $confirm_body .= "info@roadrescueservice.cz\n";

        $confirm_mail->isHTML(false);
        $confirm_mail->Subject = $confirm_subject;
        $confirm_mail->Body = $confirm_body;

        $confirm_mail->send();
    } catch (Exception $e) {
        // Pokud se nepodaří odeslat potvrzovací email, zalogujeme chybu ale nehlásíme ji uživateli
        error_log('Confirmation email error: ' . $e->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Zpráva byla úspěšně odeslána!']);

} catch (Exception $e) {
    error_log('Email error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Omlouváme se, ale zprávu se nepodařilo odeslat. Zkuste to prosím později nebo nás kontaktujte telefonicky.']);
}
?>