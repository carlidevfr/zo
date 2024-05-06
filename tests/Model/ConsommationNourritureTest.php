<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/ConsommationNourriture.php';


class ConsommationNourritureTest extends TestCase
{
   // Teste la méthode getAllNourritureNames de la classe ConsommationNourriture
   public function testgetAllNourritureNames()
   {

   $RapportInstance = new ConsommationNourriture();

    // Appeler la méthode à tester
    $RpportNames = $RapportInstance->getAllNourritureNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($RpportNames, 'La méthode getAllNourritureNames devrait retourner un tableau.');
   }

   public function testgetSearchNourritureNames()
   // Teste la méthode getSearchNourritureNames de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getSearchNourritureNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getSearchNourritureNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllNourritureNames()
   // Teste la méthode getPaginationAllNourritureNames de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getPaginationAllNourritureNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getPaginationAllNourritureNames devrait retourner un tableau.');
   }

   public function testgetByNourritureId()
   // Teste la méthode getByNourritureId de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getByNourritureId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getByNourritureId devrait retourner un tableau.');
   }

   public function testaddNourriture()
   // Teste la méthode addNourriture de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->addNourriture('2024-05-10T12:30',1,'top','top');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Cette nourriture a bien été enregistrée : top', $RapportNames);
   }

   public function testEmptyaddNourriture()
   // Teste la méthode addNourriture de la classe ConsommationNourriture avec une valeur vide
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->addNourriture('','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Veuillez fournir toutes les informations nécessaires.', $RapportNames);
   }

   public function testupdateNourriture()
   // Teste la méthode updateNourriture de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $result = $RapportInstance->updateNourriture(1, '2024-05-10T12:30', 2, '$addSante', '$addNou');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals("Le nourrissage a été mis à jour avec succès.", $result);   
   }

   public function testupdateNourritureEmpty()
   // Teste la méthode updateNourriture de la classe ConsommationNourriture vide
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $result = $RapportInstance->updateNourriture('', '', '', '', '');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals("une erreur est survenue", $result);   
   }

   public function testdeleteNourriture()
   // Teste la méthode deleteNourriture de la classe ConsommationNourriture
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $result = $RapportInstance->deleteNourriture(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La ligne de nourrissage a bien été supprimée ', $result);
   }

   public function testdeleteNourritureEmpty()
   // Teste la méthode deleteNourriture de la classe ConsommationNourriture avec une valeur vide
   {

      $RapportInstance = new ConsommationNourriture();

      // Appeler la méthode à tester
      $result = $RapportInstance->deleteNourriture('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}