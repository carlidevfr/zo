<?php

require_once './src/Model/Animaux.php';
require_once './src/Model/Habitat.php';
require_once './src/Model/Race.php';
require_once './src/Model/Common/Security.php';

class UtilisateurAnimauxController
{
    private $Animaux;
    private $Security;
    private $Race;
    private $Habitat;



    public function __construct()
    {
        $this->Animaux = new Animaux();
        $this->Race = new Race();
        $this->Habitat = new Habitat();
        $this->Security = new Security();
    }

    public function adminAnimauxPage()
    // Accueil admin de la section animaux
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère  la pagination
        (isset($_GET['page']) and !empty($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 50;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) {
            $animaux = $this->Animaux->getSearchActiveAnimauxNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();

        } else {
            $animaux = $this->Animaux->getPaginationAllActiveAnimauxNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Animaux->getAllActiveAnimauxNames())) {
            $pageMax = ceil(count($this->Animaux->getAllActiveAnimauxNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // Récupère les habitats et les races
        $races = $this->Race->getAllRaceNames();
        $habitats = $this->Habitat->getAllHabitatNames();


        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAnimaux.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'animaux',
            'elements' => $animaux,
            'races' => $races,
            'habitat' => $habitats,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-animaux/delete',
            'addUrl' => 'admin/manage-animaux/add',
            'updateUrl' => 'admin/manage-animaux/update',
            'previousUrl' => 'admin/manage-animaux',
            'token' => $token
        ]);
    }

    public function adminSuccessActionAnimaux()
    // Résultat succès ou echec après action sur animal
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        $res = null;
        $idElement = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // On récupère la liste des éléments liés pouvant empêcher la suppression
            //(isset($idElement) and !empty($idElement) ? $data = $this->Animaux->getRelatedAnimaux($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page animaux
            header('Location: ' . BASE_URL . 'admin/manage-animaux');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAnimaux.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'animaux',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-animaux/delete',
            'addUrl' => 'admin/manage-animaux/add',
            'updateUrl' => 'admin/manage-animaux/update',
            'previousUrl' => 'admin/manage-animaux'
        ]);

    }

    public function adminAddAnimaux()
    // Ajout d'un animal'
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le nouvel animal ajouté et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom 
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $animauxName = $this->Security->filter_form($_POST['addElementName']) : $animauxName = '';

            //l'état
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $animauxDesc = $this->Security->filter_form($_POST['addElementDesc']) : $animauxDesc = '';

            //l'habitat
            (isset($_POST['addElementHabitat']) and !empty($_POST['addElementHabitat'])) ? $animauxHab = $this->Security->filter_form($_POST['addElementHabitat']) : $animauxHab = '';
            
            //la race
            (isset($_POST['addElementRace']) and !empty($_POST['addElementRace'])) ? $animauxRace = $this->Security->filter_form($_POST['addElementRace']) : $animauxRace = '';
            
            //les images
            (isset($_FILES['addElementImg']) and !empty($_FILES['addElementImg'])) ? $animauxImg = $this->Security->verifyImg($_FILES['addElementImg']) : $animauxImg = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Animaux->addAnimal($animauxName, $animauxDesc, $animauxHab, $animauxRace, $animauxImg);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-animaux/action/success');
        exit;
    }

    public function adminArchiveAnimaux()
    // Archive des animaux
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id animaux à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $AniAction = $this->Security->filter_form($_POST['deleteElementId']) : $AniAction = '';


        // on fait la modification en BDD et on récupère le résultat
        $res = $this->Animaux->ArchiveAnimal($AniAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $AniAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-animaux/action/success');
        exit;
    }

    public function adminDeleteAnimauxImg()
    // Suppression de l'image des animaux
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id image à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $ImgAction = $this->Security->filter_form($_POST['deleteElementId']) : $ImgAction = '';


        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Animaux->deleteImg($ImgAction);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function adminUpdateAnimauxPage()
    // Page permettant la saisie pour la modification des animaux'
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id animaux à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $animauxAction = $this->Security->filter_form($_GET['UpdateElementId']) : $animauxAction = '';

        // Récupère l'animaux à modifier
        $animaux = $this->Animaux->getByAnimalId($animauxAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurAnimaux.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'animaux',
            'elements' => $animaux,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-animaux/delete',
            'deleteUrlImg' => 'admin/manage-animaux/deleteimg',
            'addUrl' => 'admin/manage-animaux/add',
            'updateUrl' => 'admin/manage-animaux/update',
            'previousUrl' => 'admin/manage-animaux',
            'token' => $token
        ]);

    }

    public function adminUpdateAnimaux()
    // Modification des animaux'
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();


        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // on récupère l'id de l'animaux à Modifier
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $habAction = $this->Security->filter_form($_POST['updateElementId']) : $habAction = '';

            // le nom 
            (isset($_POST['updatedName']) and !empty($_POST['updatedName'])) ? $animauxName = $this->Security->filter_form($_POST['updatedName']) : $animauxName = '';

            //la description
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $animauxDesc = $this->Security->filter_form($_POST['addElementDesc']) : $animauxDesc = '';

            //les images
            (isset($_FILES['addElementImg']) and !empty($_FILES['addElementImg'])) ? $animauxImg = $this->Security->verifyImg($_FILES['addElementImg']) : $animauxImg = '';

            // on fait la modif en BDD et on récupère le résultat
            $res = $this->Animaux->updateAnimaux($habAction, $animauxName, $animauxDesc, $animauxImg);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-animaux/action/success');
        exit;


    }
}