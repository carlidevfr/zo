<?php

require_once './src/Model/CountAnimal.php';
require_once './src/Model/Common/Security.php';

class UtilisateurStatsController
{
    private $CountAnimal;
    private $Security;

    public function __construct()
    {
        $this->CountAnimal = new CountAnimal();
        $this->Security = new Security();
    }

    public function adminStatsPage()
    // Accueil admin de la section stats
    {

        //On vérifie si on a le droit d'être là
        $this->Security->verifyAccess();

        // On récupère le role
        $userRole = $this->Security->getRole();

        if ($userRole !== 'admin') {
            $this->Security->logout();
        }

        // récupération des stats

        $res = $this->CountAnimal->getAllAnimaux();
        
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/Templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('utilisateurStats.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'Statistiques',
            'elements' => $res,
        ]);
    }

}