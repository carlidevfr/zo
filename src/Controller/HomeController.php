<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';
require_once './src/Model/Animaux.php';
require_once './src/Model/Habitat.php';
require_once './src/Model/Service.php';




class HomeController
{
    private $Security;
    private $Animaux;
    private $Habitat;
    private $Service;



    private $Regenerate;

    public function __construct(){
        $this->Security = new Security();
        $this->Regenerate = new Regenerate();
        $this->Animaux = new Animaux();
        $this->Habitat = new Habitat();
        $this->Service = new Service();


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

    public function apiGetImgAnimaux(){

        //récupération et envoi des 1ere images animaux en json
        //On veut les images des 15 premiers animaux afin d'alléger la page
        $res = $this->Animaux->getRandomActiveAnimauxNamesWithFirstImg(15);
        Model::sendJSON($res) ;     
    }

    public function apiGetImgHabitats(){

        //récupération et envoi des 1ere images habitats en json
        $res = $this->Habitat->getAllHabitatsNamesWithFirstImg();
        Model::sendJSON($res) ;     
    }

    public function apiGetServices(){

        //récupération et envoi des services en json
        $res = $this->Service->getAllServiceNames();
        Model::sendJSON($res) ;     
    }
}
