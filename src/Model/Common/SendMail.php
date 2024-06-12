<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once './src/Model/Common/Model.php';

class SendMail extends Model{
    private string $from;
    private string $to;
    private string $nom;
    private string $email;
    private string $message;
    public function __construct(string $from, string $to, string $nom, string $email, string $message)
    {
        $this->from = $from;
        $this->to = $to;
        $this->nom = $nom;
        $this->email = $email;
        $this->message = $message;
    }

    private function headers():string
    {
        return 'From: ' . $this->from . "\r\n" .
        'Reply-To: ' . $this->to . "\r\n" .
        'Content-type: text/html; charset="UTF-8' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    }

    private function contentCleaner($element):string
    {
        return implode("\r\n",str_split($element, 70));
    }

    private function mailContent(): string{
       $res =  "Information de ARCADIA" . "\r\n" . $this->contentCleaner($this->nom) . "\r\n" . "<br />" . $this->contentCleaner($this->email) . "\r\n" . "<br />" . $this->contentCleaner($this->message);
       return $res;
    }

    public function mailSend() {
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV["SMTPHOST"]; // serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["FROM"]; 
            $mail->Password = $_ENV["SMTPPASS"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
            $mail->Port = $_ENV["SMTPPORT"] ; // Port TCP à utiliser

            // Destinataires
            $mail->setFrom($this->from);
            $mail->addAddress($this->to); // Ajouter un destinataire

            // Contenu du mail
            $mail->isHTML(true); // Format d'email en HTML
            $mail->Subject = 'ZOO_CONTACT';
            $mail->Body    = $this->mailContent();
            $mail->AltBody = strip_tags($this->mailContent());

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->logError($e);
            return false;
        }
    }
}