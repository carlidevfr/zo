<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Animaux.php';


class animauxTest extends TestCase
{
   // Teste la méthode getAllActiveAnimauxNames de la classe Animaux
   public function testgetAllActiveAnimauxNames()
   {

    $AnimauxInstance = new Animaux();

    // Appeler la méthode à tester
    $raceNames = $AnimauxInstance->getAllActiveAnimauxNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($raceNames, 'La méthode getAllActiveAnimauxNames devrait retourner un tableau.');
   }

   public function testgetSearchActiveAnimauxNames()
   // Teste la méthode getSearchActiveAnimauxNames de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $AnimauxNames = $AnimauxInstance->getSearchActiveAnimauxNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($AnimauxNames, 'La méthode getSearchActiveAnimauxNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllActiveAnimauxNames()
   // Teste la méthode getPaginationAllActiveAnimauxNames de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $AnimauxNames = $AnimauxInstance->getPaginationAllActiveAnimauxNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($AnimauxNames, 'La méthode getPaginationAllActiveAnimauxNames devrait retourner un tableau.');
   }

   public function testgetByAnimalId()
   // Teste la méthode getByAnimalId de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $AnimauxNames = $AnimauxInstance->getByAnimalId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($AnimauxNames, 'La méthode getByAnimalId devrait retourner un tableau.');
   }

   public function testaddAnimal()
   // Teste la méthode addAnimal de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->addAnimal('Nouveau','au top', '1','1','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Animal ajouté avec succès avec ses images.', $result);
   }

   public function testEmptyaddAnimal()
   // Teste la méthode addAnimal de la classe Animaux avec une valeur vide
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->addAnimal('','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('une erreur est survenue', $result);
   }

   public function testupdateAnimaux()
   // Teste la méthode updateAnimaux de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->updateAnimaux(1,'nouveau nom','bof','2','2','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Animal modifié avec succès avec ses images.', $result);   
   }

   public function testupdateAnimauxEmpty()
   // Teste la méthode updateAnimaux de la classe Animaux avec un changement vide
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->updateAnimaux('','','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('une erreur est survenue', $result);
   }

   public function testdeleteImg()
   // Teste la méthode deleteImg de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->deleteImg(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('cette image a bien été supprimée ', $result);
   }

   public function testdeleteImgEmpty()
   // Teste la méthode deleteImg de la classe Animaux avec une valeur vide
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->deleteImg('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testArchiveAnimal()
   // Teste la méthode ArchiveAnimal de la classe Animaux
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->ArchiveAnimal(1);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Cet animal a bien été archivé avec ses photos ', $result);
   }

   public function testArchiveAnimalEmpty()
   // Teste la méthode ArchiveAnimal de la classe Animaux avec une valeur vide
   {

      $AnimauxInstance = new Animaux();

      // Appeler la méthode à tester
      $result = $AnimauxInstance->ArchiveAnimal('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

}