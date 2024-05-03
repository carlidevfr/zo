<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/RapportVeterinaire.php';


class RapportVeterinaireTest extends TestCase
{
   // Teste la méthode getAllRapportNames de la classe rapport veterinaire
   public function testgetAllRapportNames()
   {

   $RapportInstance = new RapportVeterinaire();

    // Appeler la méthode à tester
    $RpportNames = $RapportInstance->getAllRapportNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($RpportNames, 'La méthode getAllRapportNames devrait retourner un tableau.');
   }

   public function testgetSearchRapportNames()
   // Teste la méthode getSearchRapportNames de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getSearchRapportNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getSearchRapportNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllRapportNames()
   // Teste la méthode getPaginationAllRapportNames de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getPaginationAllRapportNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getPaginationAllRapportNames devrait retourner un tableau.');
   }

   public function testgetByRapportId()
   // Teste la méthode getByRapportId de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->getByRapportId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($RapportNames, 'La méthode getByRapportId devrait retourner un tableau.');
   }

   public function testaddRapport()
   // Teste la méthode addRapport de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->addRapport('2023/02/01',1,'top','top','top','top');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals("Le rapport vétérinaire a été ajouté avec succès et l'état de l'animal a été modifié.", $RapportNames);
   }

   public function testEmptyaddRapport()
   // Teste la méthode addRapport de la classe rapport veterinaire avec une valeur vide
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $RapportNames = $RapportInstance->addRapport('','','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Veuillez fournir toutes les informations nécessaires.', $RapportNames);
   }

   public function testupdateRapport()
   // Teste la méthode updateRapport de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $result = $RapportInstance->updateRapport(1, '2024/02/28', 2, '$addSante', '$addNou', '$addQte', '$addRapport');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals("Le rapport vétérinaire a été mis à jour avec succès.", $result);   
   }

   public function testupdateRapportEmpty()
   // Teste la méthode updateRapport de la classe rapport veterinaire vide
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $result = $RapportInstance->updateRapport('', '', '', '', '', '', '');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals("une erreur est survenue", $result);   
   }

   public function testdeleteRapport()
   // Teste la méthode deleteRapport de la classe rapport veterinaire
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $result = $RapportInstance->deleteRapport(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le rapport a bien été supprimé ', $result);
   }

   public function testdeleteRapportEmpty()
   // Teste la méthode deleteRapport de la classe rapport veterinaire avec une valeur vide
   {

      $RapportInstance = new RapportVeterinaire();

      // Appeler la méthode à tester
      $result = $RapportInstance->deleteRapport('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}