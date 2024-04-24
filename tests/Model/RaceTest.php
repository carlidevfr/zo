<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Race.php';


class RaceTest extends TestCase
{
   // Teste la méthode getAllRaceNames de la classe Race
   public function testgetAllRaceNames()
   {

    $RaceInstance = new Race();

    // Appeler la méthode à tester
    $raceNames = $RaceInstance->getAllRaceNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($raceNames, 'La méthode getAllRaceNames devrait retourner un tableau.');
   }

   public function testgetSearchRaceNames()
   // Teste la méthode getSearchRaceNames de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $RaceNames = $RaceInstance->getSearchRaceNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RaceNames, 'La méthode getSearchRaceNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllRaceNames()
   // Teste la méthode getPaginationAllRaceNames de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $RaceNames = $RaceInstance->getPaginationAllRaceNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($RaceNames, 'La méthode getPaginationAllRaceNames devrait retourner un tableau.');
   }

   public function testgetByRaceId()
   // Teste la méthode getByRaceId de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $RaceNames = $RaceInstance->getByRaceId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($RaceNames, 'La méthode getByRaceId devrait retourner un tableau.');
   }

   public function testaddRace()
   // Teste la méthode addRace de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->addRace('Nouveau');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La race suivante a bien été ajoutée : Nouveau', $result);
   }

   public function testEmptyaddRace()
   // Teste la méthode addRace de la classe Race avec une valeur vide
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->addRace('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateRace()
   // Teste la méthode updateRace de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->updateRace(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La race a bien été modifiée : nouveau nom', $result);   
   }

   public function testupdateRaceEmpty()
   // Teste la méthode updateRace de la classe Race avec un changement vide
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->updateRace(1,'');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeleteRace()
   // Teste la méthode deleteRace de la classe Race
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->deleteRace(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La race a bien été supprimée ', $result);
   }

   public function testdeleteRaceEmpty()
   // Teste la méthode deleteRace de la classe Race avec une valeur vide
   {

      $RaceInstance = new Race();

      // Appeler la méthode à tester
      $result = $RaceInstance->deleteRace('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}