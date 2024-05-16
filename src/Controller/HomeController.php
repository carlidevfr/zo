<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';
require_once './src/Model/Animaux.php';
require_once './src/Model/Habitat.php';
require_once './src/Model/Service.php';
require_once './src/Model/RapportVeterinaire.php';





class HomeController
{
    private $Security;
    private $Animaux;
    private $Habitat;
    private $Service;
    private $RapportVeterinaire;




    private $Regenerate;

    public function __construct(){
        $this->Security = new Security();
        $this->Regenerate = new Regenerate();
        $this->Animaux = new Animaux();
        $this->Habitat = new Habitat();
        $this->Service = new Service();
        $this->RapportVeterinaire = new RapportVeterinaire();
    }

    public function index(){
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('home.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

    public function servicePage(){
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('servicePage.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

    public function habitatsPage(){
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('habitatsPage.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

    public function habitatByIdPage(){
        //récupération et envoi d'un habitat selon son id

        (isset($_GET['habitat'])) ? $habitatId = $this->Security->filter_form($_GET['habitat']) : $habitatId = '';
        $res = $this->Habitat->getByHabitatId($habitatId);

        
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('habitatByIdPage.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
            'res' => $res
        ]);    
    }

    public function animalByIdPage(){
        //récupération et envoi d'un habitat selon son id

        (isset($_GET['animal'])) ? $animalId = $this->Security->filter_form($_GET['animal']) : $animalId = '';
        $res = $this->Animaux->getByAnimalId($animalId);

        
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('animalByIdPage.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
            'res' => $res
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

    public function apiGetAnimauxByHabitat(){
        //récupération et envoi des animaux d'un habitat en json
        (isset($_GET['habitat'])) ? $habitatId = $this->Security->filter_form($_GET['habitat']) : $habitatId = '';
        $res = $this->Animaux->getActiveAnimauxNamesByHabitat($habitatId);
        Model::sendJSON($res) ;     
    }

    public function apiGetAnimauxByIdAnimal(){
        //récupération et envoi de l'animal selon un id en json
        (isset($_GET['animal'])) ? $animalId = $this->Security->filter_form($_GET['animal']) : $animalId = '';
        $res = $this->Animaux->getByAnimalId($animalId);
        Model::sendJSON($res) ;     
    }

    public function apiGetRapportByIdAnimal(){
        //récupération et envoi des rapports vétérinaires selon un id animalen json
        (isset($_GET['animal'])) ? $animalId = $this->Security->filter_form($_GET['animal']) : $animalId = '';
        $res = $this->RapportVeterinaire->getByRapportByIdAnimal($animalId);
        Model::sendJSON($res) ;     
    }

    public function apiGetImgHabitats(){

        //récupération et envoi des 1ere images habitats en json
        $res = $this->Habitat->getAllHabitatsNamesWithFirstImg();
        Model::sendJSON($res) ;     
    }

    public function apiGetAllHabitats(){        
        //récupération et envoi des habitats en json

        $res = $this->Habitat->getAllHabitatNames();
        Model::sendJSON($res) ;     
    }


    public function apiGetServices(){

        //récupération et envoi des services en json
        $res = $this->Service->getAllServiceNames();
        Model::sendJSON($res) ;     
    }
}
