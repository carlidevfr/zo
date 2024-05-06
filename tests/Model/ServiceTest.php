<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Service.php';


class ServiceTest extends TestCase
{
   // Teste la méthode getAllServiceNames de la classe Service
   public function testgetAllServiceNames()
   {

    $ServiceInstance = new Service();

    // Appeler la méthode à tester
    $raceNames = $ServiceInstance->getAllServiceNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($raceNames, 'La méthode getAllServiceNames devrait retourner un tableau.');
   }

   public function testgetSearchServiceNames()
   // Teste la méthode getSearchServiceNames de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $ServiceNames = $ServiceInstance->getSearchServiceNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($ServiceNames, 'La méthode getSearchServiceNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllServiceNames()
   // Teste la méthode getPaginationAllServiceNames de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $ServiceNames = $ServiceInstance->getPaginationAllServiceNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($ServiceNames, 'La méthode getPaginationAllServiceNames devrait retourner un tableau.');
   }

   public function testgetByServiceId()
   // Teste la méthode getByServiceId de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $ServiceNames = $ServiceInstance->getByServiceId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($ServiceNames, 'La méthode getByServiceId devrait retourner un tableau.');
   }

   public function testaddService()
   // Teste la méthode addService de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->addService('Nouveau', 'tt');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le service suivant a bien été ajoutée : Nouveau', $result);
   }

   public function testEmptyaddService()
   // Teste la méthode addService de la classe Service avec une valeur vide
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->addService('','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateService()
   // Teste la méthode updateService de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->updateService(1,'nouveau nom','tt');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le service a été mis à jour avec succès.', $result);   
   }

   public function testupdateServiceEmpty()
   // Teste la méthode updateService de la classe Service avec un changement vide
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->updateService(1,'','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Une erreur est survenue', $result);
   }

   public function testdeleteService()
   // Teste la méthode deleteService de la classe Service
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->deleteService(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le service a bien été supprimé ', $result);
   }

   public function testdeleteServiceEmpty()
   // Teste la méthode deleteService de la classe Service avec une valeur vide
   {

      $ServiceInstance = new Service();

      // Appeler la méthode à tester
      $result = $ServiceInstance->deleteService('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}