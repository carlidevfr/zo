<?php
require_once './src/Model/Common/Model.php';

class CountAnimal extends Model
{
    public function addAnimalCount($id, $nom)
    {
        try {
            // Récupérer la collection "animaux"
            $collection = $this->getCollectionAnimaux();

            // Convertir l'ID en entier en cas de besoin
            $id = (int) $id;

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

            // Convertir l'ID en entier en cas de besoin
            $id = (int) $id;
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

    public function deleteAnimalById($id)
    {
        try {
            // Récupérer la collection "animaux"
            $collection = $this->getCollectionAnimaux();

            // Convertir l'ID en entier en cas de besoin
            $id = (int) $id;

            // Supprimer l'animal avec l'ID spécifié
            $result = $collection->deleteOne(['idAnimal' => $id]);

            // Vérifier si la suppression a réussi
            if ($result->getDeletedCount() > 0) {
                // La suppression a réussi
                return true;
            } else {
                // Aucun animal n'a été supprimé (peut-être que l'animal avec cet ID n'existe pas)
                return false;
            }
        } catch (Exception $e) {
            // En cas d'erreur, enregistrer l'erreur dans le fichier de log
            $this->logError($e);
            // Retourner false pour indiquer une erreur
            return false;
        }
    }

    public function getAllAnimaux()
    {
        try {
            // Récupérer la collection "animaux"
            $collection = $this->getCollectionAnimaux();

            // Récupérer toutes les données de la collection, triées par ordre décroissant de la valeur du compteur
            $animaux = $collection->find([], ['sort' => ['compteur' => -1]]);

            // Récupérer un tableau
            $res = [];

            // Parcourir chaque document
            foreach ($animaux as $animal) {
                // Extraire les champs nécessaires et les stocker dans un tableau
                $animalData = [
                    'idAnimal' => $animal['idAnimal'],
                    'nom' => $animal['nom'],
                    'compteur' => $animal['compteur']
                ];

                // Ajouter le tableau d'animal extrait au tableau résultant
                $res[] = $animalData;
            }

            return $res;
        } catch (Exception $e) {
            // En cas d'erreur, enregistrer l'erreur dans le fichier de log
            $this->logError($e);
            // Retourner null pour indiquer une erreur
            return null;
        }
    }
}