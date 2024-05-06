<?php

require_once './src/Model/ConsommationNourriture.php';
require_once './src/Model/Common/Security.php';

class ConsommationNourritureController
{
    private $ConsommationNourriture;
    private $Security;
    private $Animaux;


    public function __construct()
    {
        $this->ConsommationNourriture = new ConsommationNourriture();
        $this->Security = new Security();
        $this->Animaux = new Animaux();

    }

    public function adminNourriturePage()
    // Accueil admin de la section ConsommationNourriture
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
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
            $nourritures = $this->ConsommationNourriture->getSearchNourritureNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();

        } else {
            $nourritures = $this->ConsommationNourriture->getPaginationAllNourritureNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->ConsommationNourriture->getAllNourritureNames())) {
            $pageMax = ceil(count($this->ConsommationNourriture->getAllNourritureNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // Récupère les animaux
        $animaux = $this->Animaux->getAllActiveAnimauxNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurNourriture.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'Nourritures vétérinaires',
            'elements' => $nourritures,
            'animaux' => $animaux,
            'pageMax' => $pageMax,
            'user_role' => $userRole,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-nourriture/delete',
            'addUrl' => 'admin/manage-nourriture/add',
            'updateUrl' => 'admin/manage-nourriture/update',
            'previousUrl' => 'admin/manage-nourriture',
            'token' => $token
        ]);
    }

    public function adminSuccessActionNourriture()
    // Résultat succès ou echec après action sur nourriture
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
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

            //Si vide on retourne sur la page nourriture
            header('Location: ' . BASE_URL . 'admin/manage-nourriture');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurNourriture.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'races',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-nourriture/delete',
            'addUrl' => 'admin/manage-nourriture/add',
            'updateUrl' => 'admin/manage-nourriture/update',
            'previousUrl' => 'admin/manage-nourriture',
        ]);

    }

    public function adminAddNourriture()
    // Ajout de nourritures
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le nouveau nourriture ajouté et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // la date
            (isset($_POST['addElementDate']) and !empty($_POST['addElementDate'])) ? $addDate = $this->Security->filter_form($_POST['addElementDate']) : $addDate = '';

            //l'animal
            (isset($_POST['addElementAnimal']) and !empty($_POST['addElementAnimal'])) ? $addAnimal = $this->Security->filter_form($_POST['addElementAnimal']) : $addAnimal = '';

            //la nourriture
            (isset($_POST['addElementNou']) and !empty($_POST['addElementNou'])) ? $addNou = $this->Security->filter_form($_POST['addElementNou']) : $addNou = '';

            //la quantité
            (isset($_POST['addElementQuant']) and !empty($_POST['addElementQuant'])) ? $addQte = $this->Security->filter_form($_POST['addElementQuant']) : $addQte = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->ConsommationNourriture->addNourriture($addDate, $addAnimal, $addNou, $addQte);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-nourriture/action/success');
        exit;
    }

    public function adminDeleteNourriture()
    // Suppression de nourriture
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id  à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['deleteElementId']) : $raceAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->ConsommationNourriture->deleteNourriture($raceAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $raceAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-nourriture/action/success');
        exit;


    }

    public function adminUpdateNourriturePage()
    // Page permettant la saisie pour la modification de la ligne de nourrissage
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id  à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $raceAction = $this->Security->filter_form($_GET['UpdateElementId']) : $raceAction = '';

        // Récupère le nourriture à modifier
        $race = $this->ConsommationNourriture->getByNourritureId($raceAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurNourriture.twig');
        
        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'Nourrissage',
            'elements' => $race,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-nourriture/delete',
            'addUrl' => 'admin/manage-nourriture/add',
            'updateUrl' => 'admin/manage-nourriture/update',
            'previousUrl' => 'admin/manage-nourriture',
            'token' => $token
        ]);

    }

    public function adminUpdateNourriture()
    // Modification du nourriture
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'employe' and $userRole !== 'admin') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id de la race à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['updateElementId']) : $raceAction = '';

        // la date
        (isset($_POST['addElementDate']) and !empty($_POST['addElementDate'])) ? $addDate = $this->Security->filter_form($_POST['addElementDate']) : $addDate = '';

        //l'animal
        (isset($_POST['addElementAnimal']) and !empty($_POST['addElementAnimal'])) ? $addAnimal = $this->Security->filter_form($_POST['addElementAnimal']) : $addAnimal = '';


        //la nourriture
        (isset($_POST['addElementNou']) and !empty($_POST['addElementNou'])) ? $addNou = $this->Security->filter_form($_POST['addElementNou']) : $addNou = '';

        //la quantité
        (isset($_POST['addElementQuant']) and !empty($_POST['addElementQuant'])) ? $addQte = $this->Security->filter_form($_POST['addElementQuant']) : $addQte = '';


        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->ConsommationNourriture->updateNourriture($raceAction, $addDate, $addAnimal, $addNou, $addQte);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-nourriture/action/success');
        exit;


    }
}