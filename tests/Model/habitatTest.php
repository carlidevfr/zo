<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Habitat.php';


class HabitatTest extends TestCase
{
   // Teste la méthode getAllHabitatNames de la classe Habitat
   public function testgetAllHabitatNames()
   {

    $HabitatInstance = new Habitat();

    // Appeler la méthode à tester
    $habitatNames = $HabitatInstance->getAllHabitatNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($habitatNames, 'La méthode getAllHabitatNames devrait retourner un tableau.');
   }

   public function testgetSearchHabitatNames()
   // Teste la méthode getSearchHabitatNames de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $HabitatNames = $HabitatInstance->getSearchHabitatNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($HabitatNames, 'La méthode getSearchHabitatNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllHabitatNames()
   // Teste la méthode getPaginationAllHabitatNames de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $HabitatNames = $HabitatInstance->getPaginationAllHabitatNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($HabitatNames, 'La méthode getPaginationAllHabitatNames devrait retourner un tableau.');
   }

   public function testgetByHabitatId()
   // Teste la méthode getByHabitatId de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $HabitatNames = $HabitatInstance->getByHabitatId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($HabitatNames, 'La méthode getByHabitatId devrait retourner un tableau.');
   }

   public function testaddHabitat()
   // Teste la méthode addHabitat de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $result = $HabitatInstance->addHabitat('Nouveau','nouveau','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Habitat ajouté avec succès avec ses images.', $result);
   }

   public function testEmptyaddHabitat()
   // Teste la méthode addHabitat de la classe Habitat avec une valeur vide
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $result = $HabitatInstance->addHabitat('','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateHabitat()
   // Teste la méthode updateHabitat de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $result = $HabitatInstance->updateHabitat(1,'nouveau nom','ttttt','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Habitat modifié avec succès avec ses images.', $result);   
   }

   public function testdeleteHabitat()
   // Teste la méthode deleteHabitat de la classe Habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $result = $HabitatInstance->deleteHabitat(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Suppression réussie', $result);
   }

   public function testdeleteHabitatEmpty()
   // Teste la méthode deleteHabitat de la classe Habitat avec une valeur vide
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $result = $HabitatInstance->deleteHabitat('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testDeleteImgWithValidId()
   {
       // Créer une instance de la classe à tester
       $HabitatInstance = new Habitat();

       // Appeler la méthode à tester avec un ID valide
       $result = $HabitatInstance->deleteImg(1);

       // Assertions
       $this->assertEquals('cette image a bien été supprimée ', $result);
   }

   public function testDeleteImgWithEmptyId()
   {
       // Créer une instance de la classe à tester
       $HabitatInstance = new Habitat();


       // Appeler la méthode à tester avec un ID vide
       $result = $HabitatInstance->deleteImg('');

       // Assertions
       $this->assertEquals(null, $result);
   }

   public function getRelatedHabitat()
   // Teste la méthode getRelatedHabitat de la classe habitat
   {

      $HabitatInstance = new Habitat();

      // Appeler la méthode à tester
      $habitatNames = $HabitatInstance->getRelatedHabitat(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($habitatNames, 'La méthode getRelatedHabitat devrait retourner un tableau.');
   }
}