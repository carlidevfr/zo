<?php

class SendMail{
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
       $res =  "Demande de contact de la part de" . "\r\n" . $this->contentCleaner($this->nom) . "\r\n" . "<br />" . $this->contentCleaner($this->email) . "\r\n" . "<br />" . $this->contentCleaner($this->message);
       return $res;
    }

    public function mailSend()
    {
        return mail($this->to,'ZOO_CONTACT',$this->mailContent(),$this->headers());  
    }

}