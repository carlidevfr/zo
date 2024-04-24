<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Avis.php';


class AvisTest extends TestCase
{
   // Teste la méthode getAllAvisNames de la classe Avis
   public function testgetAllAvisNames()
   {

    $AvisInstance = new Avis();

    // Appeler la méthode à tester
    $avisNames = $AvisInstance->getAllAvisNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($avisNames, 'La méthode getAllAvisNames devrait retourner un tableau.');
   }

   public function testgetSearchAvisNames()
   // Teste la méthode getSearchAvisNames de la classe Avis
   {

      $AvisInstance = new Avis();

      // Appeler la méthode à tester
      $AvisNames = $AvisInstance->getSearchAvisNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($AvisNames, 'La méthode getSearchAvisNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllAvisNames()
   // Teste la méthode getPaginationAllAvisNames de la classe Avis
   {

      $AvisInstance = new Avis();

      // Appeler la méthode à tester
      $AvisNames = $AvisInstance->getPaginationAllAvisNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($AvisNames, 'La méthode getPaginationAllAvisNames devrait retourner un tableau.');
   }

   public function testgetByAvisId()
   // Teste la méthode getByAvisId de la classe Avis
   {

      $AvisInstance = new Avis();

      // Appeler la méthode à tester
      $AvisNames = $AvisInstance->getByAvisId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($AvisNames, 'La méthode getByAvisId devrait retourner un tableau.');
   }

   public function testupdateAvis()
   // Teste la méthode updateAvis de la classe Avis
   {

      $AvisInstance = new Avis();

      // Appeler la méthode à tester
      $result = $AvisInstance->updateAvis(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le statut de cet avis a bien été modifié ', $result);   
   }

}