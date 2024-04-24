<?php

require_once './src/Model/Race.php';
require_once './src/Model/Common/Security.php';

class UtilisateurRaceController
{
    private $Race;
    private $Security;

    public function __construct()
    {
        $this->Race = new Race();
        $this->Security = new Security();
    }

    public function adminRacePage()
    // Accueil admin de la section race
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
            $races = $this->Race->getSearchRaceNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
            
        } else {
            $races = $this->Race->getPaginationAllRaceNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Race->getAllRaceNames())) {
            $pageMax = ceil(count($this->Race->getAllRaceNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }
        
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRace.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'races',
            'elements' => $races,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-race/delete',
            'addUrl' => 'admin/manage-race/add',
            'updateUrl' => 'admin/manage-race/update',
            'previousUrl' => 'admin/manage-race',
            'token' => $token
        ]);
    }

    public function adminSuccessActionRace()
    // Résultat succès ou echec après action sur race
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

            //Si vide on retourne sur la page pays
            header('Location: ' . BASE_URL . 'admin/manage-race');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRace.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'races',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-race/delete',
            'addUrl' => 'admin/manage-race/add',
            'updateUrl' => 'admin/manage-race/update',
            'previousUrl' => 'admin/manage-race'
        ]);

    }

    public function adminAddRace()
    // Ajout de races
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

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la race ajoutée
        (isset($_POST['addElementName']) and !empty($_POST['addElementName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['addElementName']) : $raceAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Race->addRace($raceAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-race/action/success');
        exit;
    }

    public function adminDeleteRace()
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
        $res = $this->Race->deleteRace($raceAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $raceAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-race/action/success');
        exit;


    }

    public function adminUpdateRacePage()
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

        //Récupère l'id du pays à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $raceAction = $this->Security->filter_form($_GET['UpdateElementId']) : $raceAction = '';

        // Récupère le pays à modifier
        $country = $this->Race->getByRaceId($raceAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRace.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'races',
            'elements' => $country,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-race/delete',
            'addUrl' => 'admin/manage-race/add',
            'updateUrl' => 'admin/manage-race/update',
            'previousUrl' => 'admin/manage-race',
            'token' => $token
        ]);

    }

    public function adminUpdateRace()
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
        $res = $this->Race->updateRace($raceAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-race/action/success');
        exit;


    }
}