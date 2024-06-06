<?php

abstract class Model
{
    private static $pdo;
    private static $mongoDb;
    private static $client;


    public function connexionPDO(){
        try {
            self::$pdo = new PDO('mysql:host=' . Security::filter_form($_ENV["DB_HOST"]) . ';dbname=' . Security::filter_form($_ENV["DB_NAME"]).';charset=utf8mb4' , Security::filter_form($_ENV["DB_USER"]), Security::filter_form($_ENV["DB_PASS"]));
            //self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return self::$pdo;
        } catch (PDOException $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
                );
                error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function connexionMongo(){
        try {
            self::$client = new MongoDB\Client(Security::filter_form($_ENV["MONGO_BASE"]) .  Security::filter_form($_ENV["MONGO_INITDB_ROOT_USERNAME"]) .':' . urlencode(Security::filter_form($_ENV["MONGO_INITDB_ROOT_PASS"])) . '@' . Security::filter_form($_ENV["MONGO_INITDB_HOST"]));
            // Sélection de la base de données
            self::$mongoDb = self::$client->STATSANIMAUX; // Remplacez "test" par le nom de votre base de données
            return self::$mongoDb;
        } catch (PDOException $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
                );
                error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getCollectionAnimaux(){
        try {
            // Connexion à MongoDB
            $this->connexionMongo();
            
            // Création de la collection "animaux"
            self::$mongoDb->createCollection("animaux");

            // Retourner la collection "animaux" pour une utilisation ultérieure
            return self::$mongoDb->animaux;
        } catch (PDOException $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
                );
                error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public static function sendJSON($info){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        echo json_encode($info);
    }

    protected function logError(Exception $e)
    {
        $log = sprintf(
            "%s %s %s %s %s",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine()
        );
        error_log($log . "\n", 3, './src/error.log');
    }
}