<?php

require_once './src/Model/RapportVeterinaire.php';
require_once './src/Model/Common/Security.php';

class UtilisateurRapportVeteController
{
    private $RapportVeterinaire;
    private $Security;
    private $Animaux;


    public function __construct()
    {
        $this->RapportVeterinaire = new RapportVeterinaire();
        $this->Security = new Security();
        $this->Animaux = new Animaux();

    }

    public function adminRapportPage()
    // Accueil admin de la section RapportVeterinaire
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
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
            $rapports = $this->RapportVeterinaire->getSearchRapportNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();

        } else {
            $rapports = $this->RapportVeterinaire->getPaginationAllRapportNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->RapportVeterinaire->getAllRapportNames())) {
            $pageMax = ceil(count($this->RapportVeterinaire->getAllRapportNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // Récupère les animaux
        $animaux = $this->Animaux->getAllActiveAnimauxNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRapport.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'Rapports vétérinaires',
            'elements' => $rapports,
            'animaux' => $animaux,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-rapport/delete',
            'addUrl' => 'admin/manage-rapport/add',
            'updateUrl' => 'admin/manage-rapport/update',
            'previousUrl' => 'admin/manage-rapport',
            'token' => $token
        ]);
    }

    public function adminSuccessActionRapport()
    // Résultat succès ou echec après action sur rapport veterinaire
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
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

            //Si vide on retourne sur la page rapport
            header('Location: ' . BASE_URL . 'admin/manage-rapport');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRapport.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'races',
            'addResult' => $res,
            'deleteUrl' => 'admin/manage-rapport/delete',
            'addUrl' => 'admin/manage-rapport/add',
            'updateUrl' => 'admin/manage-rapport/update',
            'previousUrl' => 'admin/manage-rapport',
        ]);

    }

    public function adminAddRapport()
    // Ajout de rapports
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le nouveau rapport ajouté et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // la date
            (isset($_POST['addElementDate']) and !empty($_POST['addElementDate'])) ? $addDate = $this->Security->filter_form($_POST['addElementDate']) : $addDate = '';

            //l'animal
            (isset($_POST['addElementAnimal']) and !empty($_POST['addElementAnimal'])) ? $addAnimal = $this->Security->filter_form($_POST['addElementAnimal']) : $addAnimal = '';

            //l'état de santé
            (isset($_POST['addElementSant']) and !empty($_POST['addElementSant'])) ? $addSante = $this->Security->filter_form($_POST['addElementSant']) : $addSante = '';

            //la nourriture
            (isset($_POST['addElementNou']) and !empty($_POST['addElementNou'])) ? $addNou = $this->Security->filter_form($_POST['addElementNou']) : $addNou = '';

            //la quantité
            (isset($_POST['addElementQuant']) and !empty($_POST['addElementQuant'])) ? $addQte = $this->Security->filter_form($_POST['addElementQuant']) : $addQte = '';

            //le rapport
            (isset($_POST['addElementRapport']) and !empty($_POST['addElementRapport'])) ? $addRapport = $this->Security->filter_form($_POST['addElementRapport']) : $addRapport = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->RapportVeterinaire->addRapport($addDate, $addAnimal, $addSante, $addNou, $addQte, $addRapport);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-rapport/action/success');
        exit;
    }

    public function adminDeleteRapport()
    // Suppression de rapport
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id pays à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $raceAction = $this->Security->filter_form($_POST['deleteElementId']) : $raceAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->RapportVeterinaire->deleteRapport($raceAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $raceAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-rapport/action/success');
        exit;


    }

    public function adminUpdateRapportPage()
    // Page permettant la saisie pour la modification de la race
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
            $this->Security->logout();
        }

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du pays à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $raceAction = $this->Security->filter_form($_GET['UpdateElementId']) : $raceAction = '';

        // Récupère le rapport à modifier
        $race = $this->RapportVeterinaire->getByRapportId($raceAction);
        $modifySection = true;

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurRapport.twig');
        
        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'Rapports vétérinaires',
            'elements' => $race,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-rapport/delete',
            'addUrl' => 'admin/manage-rapport/add',
            'updateUrl' => 'admin/manage-rapport/update',
            'previousUrl' => 'admin/manage-rapport',
            'token' => $token
        ]);

    }

    public function adminUpdateRapport()
    // Modification du rapport
    {
        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'veterinaire') {
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

        //l'état de santé
        (isset($_POST['addElementSant']) and !empty($_POST['addElementSant'])) ? $addSante = $this->Security->filter_form($_POST['addElementSant']) : $addSante = '';

        //la nourriture
        (isset($_POST['addElementNou']) and !empty($_POST['addElementNou'])) ? $addNou = $this->Security->filter_form($_POST['addElementNou']) : $addNou = '';

        //la quantité
        (isset($_POST['addElementQuant']) and !empty($_POST['addElementQuant'])) ? $addQte = $this->Security->filter_form($_POST['addElementQuant']) : $addQte = '';

        //le rapport
        (isset($_POST['addElementRapport']) and !empty($_POST['addElementRapport'])) ? $addRapport = $this->Security->filter_form($_POST['addElementRapport']) : $addRapport = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->RapportVeterinaire->updateRapport($raceAction, $addDate, $addAnimal, $addSante, $addNou, $addQte, $addRapport);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-rapport/action/success');
        exit;


    }
}