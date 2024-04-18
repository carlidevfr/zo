<?php

require_once './src/Model/Common/Security.php';
require_once './src/Model/Utilisateur.php';


class UtilisateurHomeController
{
    private $Security;
    private $Utilisateur;

    public function __construct()
    {
        $this->Security = new Security();
        $this->Utilisateur = new Utilisateur();
    }

}
