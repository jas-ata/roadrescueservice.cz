<?php
header('Content-Type: application/json');

// Konfigurace
$to_email = "info@roadrescueservice.cz";
$subject_prefix = "Webový formulář - ";

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

// Příprava emailu
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

// Hlavičky emailu
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Odeslání emailu
try {
    if (mail($to_email, $email_subject, $email_body, $headers)) {
        // Odeslání kopie odesílateli
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
        
        $confirm_headers = "From: Road Rescue Service <$to_email>\r\n";
        $confirm_headers .= "Reply-To: $to_email\r\n";
        $confirm_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        mail($email, $confirm_subject, $confirm_body, $confirm_headers);
        
        echo json_encode(['success' => true, 'message' => 'Zpráva byla úspěšně odeslána!']);
    } else {
        throw new Exception('Chyba při odesílání emailu.');
    }
} catch (Exception $e) {
    error_log('Email error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Omlouváme se, ale zprávu se nepodařilo odeslat. Zkuste to prosím později nebo nás kontaktujte telefonicky.']);
}
?>