<?php
session_start();

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Security.php';


class SecurityTest extends TestCase
{
    public function testFilterForm()
    {
        // Cas où la donnée est une chaîne vide
        $data = '';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('', $filtered_data);

        // Cas où la donnée est une chaîne avec des espaces
        $data = '   test   ';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('test', $filtered_data);

        // Cas où la donnée contient des caractères spéciaux
        $data = '<script>alert("XSS");</script>';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;', $filtered_data);
    }

    public function testVerifyToken()
    {
        // Cas où les tokens sont identiques
        $token = 'valid_token';
        $form = 'valid_token';
        $this->assertTrue(Security::verifyToken($token, $form));

        // Cas où les tokens sont différents
        $token = 'valid_token';
        $form = 'invalid_token';
        $this->assertFalse(Security::verifyToken($token, $form));

        // Cas où l'un des tokens est vide
        $token = '';
        $form = 'valid_token';
        $this->assertFalse(Security::verifyToken($token, $form));

        // Cas où l'un des tokens est NULL
        $token = null;
        $form = 'valid_token';
        $this->assertFalse(Security::verifyToken($token, $form));
    }

    public function testGetRoleWithRoleSet()
    {
        // Crée une session fictive avec un rôle défini
        $_SESSION['role'] = 'admin';

        // Appelle la méthode getRole()
        $role = Security::getRole();

        // Vérifie que le rôle retourné correspond au rôle de la session (filtré par filter_form)
        $this->assertEquals('admin', $role);
    }

    public function testGetTokenWithCsrfTokenSet()
    {
        // Définir un token CSRF dans la session pour le test
        $_SESSION['csrf_token'] = 'abcdef123456';

        // Appeler la méthode getToken() pour récupérer le token CSRF
        $token = Security::getToken();

        // Vérifier si le token retourné correspond au token défini dans la session
        $this->assertEquals('abcdef123456', $token);
    }
}
