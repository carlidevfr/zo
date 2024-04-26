<?php

require_once './src/Model/Habitat.php';
require_once './src/Model/Common/Security.php';

class UtilisateurHabitatController
{
    private $Habitat;
    private $Security;

    public function __construct()
    {
        $this->Habitat = new Habitat();
        $this->Security = new Security();
    }

    public function adminHabitatPage()
    // Accueil admin de la section habitat
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
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) {
            $habitat = $this->Habitat->getSearchHabitatNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
            
        } else {
            $habitat = $this->Habitat->getPaginationAllHabitatNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Habitat->getAllHabitatNames())) {
            $pageMax = ceil(count($this->Habitat->getAllHabitatNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitat.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'elements' => $habitat,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-habitat/delete',
            'addUrl' => 'admin/manage-habitat/add',
            'updateUrl' => 'admin/manage-habitat/update',
            'previousUrl' => 'admin/manage-habitat',
            'token' => $token
        ]);
    }

    public function adminSuccessActionHabitat()
    // Résultat succès ou echec après action sur habitat
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

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page habitat
            header('Location: ' . BASE_URL . 'admin/manage-habitat');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitat.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-habitat/delete',
            'addUrl' => 'admin/manage-habitat/add',
            'updateUrl' => 'admin/manage-habitat/update',
            'previousUrl' => 'admin/manage-habitat'
        ]);

    }

    public function adminAddHabitat()
    // Ajout de habitat
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

        // on récupère le nouvel habitat ajouté et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom 
            (isset ($_POST['addElementName']) and !empty ($_POST['addElementName'])) ? $habitatName = $this->Security->filter_form($_POST['addElementName']) : $habitatName = '';

            //la description
            (isset ($_POST['addElementDesc']) and !empty ($_POST['addElementDesc'])) ? $habitatDesc = $this->Security->filter_form($_POST['addElementDesc']) : $habitatDesc = '';

            //les images
            (isset ($_FILES['addElementImg']) and !empty ($_FILES['addElementImg'])) ? $habitatImg = $this->Security->verifyImg($_FILES['addElementImg']) : $habitatImg = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Habitat->addHabitat($habitatName, $habitatDesc, $habitatImg);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-habitat/action/success');
        exit;
    }

    public function adminDeleteHabitat()
    // Suppression de race
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

        // on récupère l'id pays à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['deleteElementId']) : $raceAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Habitat->deleteHabitat($raceAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $raceAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-habitat/action/success');
        exit;
    }

    public function adminUpdateHabitatPage()
    // Page permettant la saisie pour la modification de la race
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

        //Récupère l'id habitat à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $raceAction = $this->Security->filter_form($_GET['UpdateElementId']) : $raceAction = '';

        // Récupère l'habitat à modifier
        $race = $this->Habitat->getByHabitatId($raceAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurHabitat.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'habitat',
            'elements' => $race,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-race/delete',
            'addUrl' => 'admin/manage-race/add',
            'updateUrl' => 'admin/manage-race/update',
            'previousUrl' => 'admin/manage-race',
            'token' => $token
        ]);

    }

    public function adminUpdateHabitat()
    // Modification de la race
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

        // on récupère l'id de la race à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['updateElementId']) : $raceAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Habitat->updateHabitat($raceAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-race/action/success');
        exit;


    }
}