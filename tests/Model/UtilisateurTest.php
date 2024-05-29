<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Utilisateur.php';


class UtilisateurTest extends TestCase
{
   // Teste la méthode getAllUtilisateurNames de la classe Utilisateur
   public function testgetAllUtilisateurNames()
   {

    $UtilisateurInstance = new Utilisateur();

    // Appeler la méthode à tester
    $raceNames = $UtilisateurInstance->getAllUtilisateurNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($raceNames, 'La méthode getAllUtilisateurNames devrait retourner un tableau.');
   }

   public function testgetSearchUtilisateurNames()
   // Teste la méthode getSearchUtilisateurNames de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $UtilisateurNames = $UtilisateurInstance->getSearchUtilisateurNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($UtilisateurNames, 'La méthode getSearchUtilisateurNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllUtilisateurNames()
   // Teste la méthode getPaginationAllUtilisateurNames de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $UtilisateurNames = $UtilisateurInstance->getPaginationAllUtilisateurNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($UtilisateurNames, 'La méthode getPaginationAllUtilisateurNames devrait retourner un tableau.');
   }

   public function testaddUtilisateur()
   // Teste la méthode addUtilisateur de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $email = rand(1,10000) . 'gerger@erge.gt';
      $result = $UtilisateurInstance->addUtilisateur('Nouveau', 'tt',$email ,'gTdrt4987,!oeydz',2);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('cet utilisateur a bien été ajouté : '. $email, $result);
   }

   public function testEmptyaddUtilisateur()
   // Teste la méthode addUtilisateur de la classe Utilisateur avec une valeur vide
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->addUtilisateur('','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Tous les champs doivent être remplis.', $result);
   }

   public function testaddUtilisateurWrongPass()
   // Teste la méthode addUtilisateur de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->addUtilisateur('Nouveau', 'tt','gerger@erge.gt','gTydz',2);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le mot de passe ne respecte pas les critères de sécurité.', $result);
   }

   public function testupdateUtilisateur()
   // Teste la méthode updateUtilisateur de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->updateUtilisateur(1,'nouveau nom','tt','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Cet utilisateur a été mis à jour avec succès.', $result);   
   }

   public function testupdateUtilisateurEmpty()
   // Teste la méthode updateUtilisateur de la classe Utilisateur avec un changement vide
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->updateUtilisateur('','','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('une erreur est survenue', $result);
   }

   public function testdeleteUtilisateur()
   // Teste la méthode deleteUtilisateur de la classe Utilisateur
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->deleteUtilisateur(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Cet utilisateur a bien été supprimé ', $result);
   }

   public function testdeleteUtilisateurEmpty()
   // Teste la méthode deleteUtilisateur de la classe Utilisateur avec une valeur vide
   {

      $UtilisateurInstance = new Utilisateur();

      // Appeler la méthode à tester
      $result = $UtilisateurInstance->deleteUtilisateur('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}