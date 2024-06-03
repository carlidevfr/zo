<?php
require_once './src/Model/Common/Model.php';

class CountAnimal extends Model
{
    public function addAnimalCount($id, $nom)
    {
        try {
            // Récupérer la collection "animaux"
            $collection = $this->getCollectionAnimaux();

            // Vérifier si l'animal existe déjà dans la collection
            $animal = $collection->findOne(['idAnimal' => $id]);

            if ($animal) {
                // Si l'animal existe, mettre à jour son compteur
                $collection->updateOne(
                    ['idAnimal' => $id],
                    ['$inc' => ['compteur' => 1]]
                );
            } else {
                // Si l'animal n'existe pas, le créer avec un compteur initial à 1
                $collection->insertOne([
                    'idAnimal' => $id,
                    'nom' => $nom,
                    'compteur' => 1
                ]);
            }

            // Retourner true pour indiquer que l'opération a réussi
            return true;
        } catch (Exception $e) {
            // En cas d'erreur, enregistrer l'erreur dans le fichier de log
            $this->logError($e);
            // Retourner false pour indiquer que l'opération a échoué
            return false;
        }
    }
    public function getAnimal($id)
    // retourne l'animal
    {
        try {
            // Récupérer la collection "animaux"
            $collection = $this->getCollectionAnimaux();
            
            // Rechercher l'animal dans la collection
            $animal = $collection->findOne(['idAnimal' => $id]);
            
            if ($animal) {
                // Si l'animal existe, retourner son compteur
                return $animal;
            } else {
                // Si l'animal n'existe pas, retourner 0
                return null;
            }
        } catch (Exception $e) {
            // En cas d'erreur, enregistrer l'erreur dans le fichier de log
            $this->logError($e);
            // Retourner -1 pour indiquer une erreur
            return null;
        }
    }
}