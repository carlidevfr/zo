<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';

class HomeController
{
    private $Security;
    private $Regenerate;

    public function __construct(){
        $this->Security = new Security();
        $this->Regenerate = new Regenerate();
    }

    public function index(){
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('home.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

    public function createBddProd(){
        
        // Création de la base de données prod
        if ($this->Regenerate->regenerateSqlProd('./src/Data/prod.sql')) {
            echo 'la base de données a été créée';
        }else{
            echo 'une erreur est survenue, consultez les logs';
        }
    }

    public function createBddTest(){
        // Importation des variables d'environnement test
        require_once './src/Config/envTest.php'; 
        // Création de la base de données TEST
        if ($this->Regenerate->regenerateSqlProd('./tests/Data/testprod.sql')) {
            echo 'la base de données a été créée';
        }else{
            echo 'une erreur est survenue, consultez les logs';
        }
    }
}
