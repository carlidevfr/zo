<?php
session_start();

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/SendMail.php';


class SecurityTest extends TestCase
{
    public function testmailSend()
    // Test de l'envoi d'un mail
    {
        $SendMailInstance = new SendMail($_ENV['FROM'], $_ENV['FROM'], 'nom', 'email', 'message');

        // Appeler la méthode à tester
        $send = $SendMailInstance->mailSend();

        // Si ok renvoie true
        $this->assertEquals(true, $send);

    }

}
