<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception as MongoDBException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class MongoController extends AbstractController
{
 /**
     * @Route("/api/add-document", name="add_document", methods={"POST","GET"})
     */
    public function addDocument(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->students;

            // Ajouter un document à la collection
            $result = $collection->updateOne(
                ['idStudent' => '4'],
                ['$set' => ['skillLevel' => 'C2']]
                // Ajoutez les champs et les valeurs que vous souhaitez ajouter
            );
// Vérifiez si la mise à jour a réussi
        if ($result->getModifiedCount() > 0) {
            // Retourner une réponse réussie
            return new Response('Skill Level mis à jour avec succès!', 200);
        } else {
            // Aucun document modifié (peut être parce que l'idStudent n'a pas été trouvé)
            return new Response('Aucun document modifié.', 404);
        }
    } catch (\Exception $e) {
        // Gérer les erreurs
        return new Response('Erreur lors de la mise à jour : ' . $e->getMessage(), 500);
    }
}
     /**
     * @Route("/api/get-document", name="get_document", methods={"GET"})
     */
        public function getDocument(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->classe;

             // Récupérer tous les documents de la collection
            $documents = $collection->find();

            // Convertir les documents en tableau pour la réponse
            $documentArray = iterator_to_array($documents);

            // Convertir le tableau en format JSON
            $jsonResponse = json_encode($documentArray);

            // Retourner une réponse JSON réussie
            return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
        } catch (MongoDBException $e) {
            // Gérer l'erreur, par exemple, en la journalisant
            return new Response('Une erreur s\'est produite lors de la récupération des documents.');
        }
    }
     /**
     * @Route("/api/get-class", name="get_class", methods={"GET"})
     */
        public function getClass(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->class;

             // Récupérer tous les documents de la collection
            $documents = $collection->find();

            // Convertir les documents en tableau pour la réponse
            $documentArray = iterator_to_array($documents);

            // Convertir le tableau en format JSON
            $jsonResponse = json_encode($documentArray);

            // Retourner une réponse JSON réussie
            return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
        } catch (MongoDBException $e) {
            // Gérer l'erreur, par exemple, en la journalisant
            return new Response('Une erreur s\'est produite lors de la récupération des documents.');
        }
    }
 /**
     * @Route("/api/get-exercises", name="get_exercises", methods={"GET"})
     */
        public function getExercises(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->exercises;

             // Récupérer tous les documents de la collection
            $documents = $collection->find();

            // Convertir les documents en tableau pour la réponse
            $documentArray = iterator_to_array($documents);

            // Convertir le tableau en format JSON
            $jsonResponse = json_encode($documentArray);

            // Retourner une réponse JSON réussie
            return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
        } catch (MongoDBException $e) {
            // Gérer l'erreur, par exemple, en la journalisant
            return new Response('Une erreur s\'est produite lors de la récupération des documents.');
        }
    }
 /**
     * @Route("/api/get-students", name="get_students", methods={"GET"})
     */
        public function getStudents(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->students;

             // Récupérer tous les documents de la collection
            $documents = $collection->find();

            // Convertir les documents en tableau pour la réponse
            $documentArray = iterator_to_array($documents);

            // Convertir le tableau en format JSON
            $jsonResponse = json_encode($documentArray);

            // Retourner une réponse JSON réussie
            return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
        } catch (MongoDBException $e) {
            // Gérer l'erreur, par exemple, en la journalisant
            return new Response('Une erreur s\'est produite lors de la récupération des documents.');
        }
    }
 /**
     * @Route("/api/get-lexical", name="get_lexical", methods={"GET"})
     */
        public function getLexical(): Response
    {
        try {
            // Créer une instance du client MongoDB
            $mongoClient = new Client('mongodb://mongo:27017');

            // Sélectionner la base de données
            $database = $mongoClient->mydatabase;

            // Sélectionner une collection (par exemple, "couleurs")
            $collection = $database->lexical;

             // Récupérer tous les documents de la collection
            $documents = $collection->find();

            // Convertir les documents en tableau pour la réponse
            $documentArray = iterator_to_array($documents);

            // Convertir le tableau en format JSON
            $jsonResponse = json_encode($documentArray);

            // Retourner une réponse JSON réussie
            return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
        } catch (MongoDBException $e) {
            // Gérer l'erreur, par exemple, en la journalisant
            return new Response('Une erreur s\'est produite lors de la récupération des documents.');
        }
    }
/**
 * @Route("/api/update-students-skill-level", name="update_students_skill_level", methods={"GET"})
 */
public function updateStudentsSkillLevel(): Response
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection des étudiants
        $studentsCollection = $database->students;

        // Sélectionner la collection des exercices
        $exercisesCollection = $database->exercises;

        // Récupérer tous les étudiants
        $students = $studentsCollection->find();

        foreach ($students as $student) {
            // Récupérer les notes des exercices pour l'étudiant
            $exerciseIds = $student['idExercises'];
            $exerciseNotes = $exercisesCollection->find(
                ['idExercises' => ['$in' => $exerciseIds]],
                ['projection' => ['exercisesSkillLevel' => 1]]
            )->toArray();

            // Convertir les notes en nombres
            $numericNotes = array_map(function ($exercise) {
                switch ($exercise['exercisesSkillLevel']) {
                    case 'A1':
                        return 1;
                    case 'A2':
                        return 2;
                    case 'B1':
                        return 3;
                    case 'B2':
                        return 4;
                    case 'C1':
                        return 5;
                    case 'C2':
                        return 6;
                    default:
                        return 0;
                }
            }, $exerciseNotes);

            // Calculer la moyenne des notes
            $average = count($numericNotes) > 0 ? array_sum($numericNotes) / count($numericNotes) : 0;

            // Calculer la médiane des notes
            sort($numericNotes);
            $count = count($numericNotes);
            $middle = floor(($count - 1) / 2);
            $median = ($numericNotes[$middle] + $numericNotes[$middle + 1 - $count % 2]) / 2;

            // Calculer l'écart type des notes
            $standardDeviation = $count > 0 ? sqrt(array_sum(array_map(function ($note) use ($average) {
                return pow($note - $average, 2);
            }, $numericNotes)) / $count) : 0;

            // Arrondir au plus près
            $roundedAverage = round($average);
            $roundedMedian = round($median);
            $roundedStandardDeviation = round($standardDeviation);

            // Mapper les valeurs arrondies au niveau de compétence
            $skillLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
            $newMoySkillLevel = $skillLevels[$roundedAverage - 1];
	    $newMedSkillLevel = $skillLevels[$roundedMedian - 1];

            // Mise à jour du skill level de l'étudiant avec médiane et écart type
            $studentsCollection->updateOne(
                ['_id' => $student['_id']],
                [
                    '$set' => [
                        'skillLevel' => [
                            ['type' => 'moyenne', 'value' => $newMoySkillLevel],
                            ['type' => 'mediane', 'value' => $newMedSkillLevel],
                            ['type' => 'ecarttype', 'value' => $roundedStandardDeviation],
                        ],
                    ],
                ]
            );
        }

        return new Response('Skill levels mis à jour avec succès.');
    } catch (\Exception $e) {
        // Gérer l'erreur, par exemple, en la journalisant
        return new Response('Une erreur s\'est produite lors de la mise à jour des skill levels.');
    }
}

/**
 * @Route("/api/update-class-skill-level", name="update_class_skill_level", methods={"GET"})
 */
public function updateClassSkillLevel(): Response
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection des classes
        $classesCollection = $database->class;

        // Récupérer toutes les classes
        $classes = $classesCollection->find();

        foreach ($classes as $class) {
            // Récupérer les étudiants de la classe
            $studentsIds = $class['studentOfClassById'];
            $numericSkillLevels = [];

            // Calculer les niveaux de compétence numériques des étudiants de la classe
            foreach ($studentsIds as $studentId) {
                $student = $database->students->findOne(['idStudent' => $studentId]);

                if ($student && isset($student['skillLevel'][0]['value'])) {
                    // Ajouter le niveau de compétence numérique au tableau
                    switch ($student['skillLevel'][0]['value']) {
                        case 'A1':
                            $numericSkillLevels[] = 1;
                            break;
                        case 'A2':
                            $numericSkillLevels[] = 2;
                            break;
                        case 'B1':
                            $numericSkillLevels[] = 3;
                            break;
                        case 'B2':
                            $numericSkillLevels[] = 4;
                            break;
                        case 'C1':
                            $numericSkillLevels[] = 5;
                            break;
                        case 'C2':
                            $numericSkillLevels[] = 6;
                            break;
                        default:
                            break;
                    }
                }
            }

            // Calculer la moyenne des niveaux de compétence de la classe
            $averageSkillLevel = count($numericSkillLevels) > 0 ? array_sum($numericSkillLevels) / count($numericSkillLevels) : 0;

            // Calculer la médiane des niveaux de compétence de la classe
            sort($numericSkillLevels);
            $count = count($numericSkillLevels);
            $middle = floor(($count - 1) / 2);
            $median = ($numericSkillLevels[$middle] + $numericSkillLevels[$middle + 1 - $count % 2]) / 2;

            // Calculer l'écart type des niveaux de compétence de la classe
            $standardDeviation = $count > 0 ? sqrt(array_sum(array_map(function ($level) use ($averageSkillLevel) {
                return pow($level - $averageSkillLevel, 2);
            }, $numericSkillLevels)) / $count) : 0;

            // Arrondir au plus près
            $roundedAverage = round($averageSkillLevel);
            $roundedMedian = round($median);
            $roundedStandardDeviation = round($standardDeviation);

            // Mapper les valeurs arrondies aux types de compétence
            $skillLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
            $newClassSkillLevel = $skillLevels[$roundedAverage - 1];
            $newClassMedian = $skillLevels[$roundedMedian - 1];

            // Mise à jour du classSkillLevel de la classe
            $classesCollection->updateOne(
                ['_id' => $class['_id']],
                [
                    '$set' => [
                        'classSkillLevel' => [
                            ['type' => 'moyenne', 'value' => $newClassSkillLevel],
                            ['type' => 'mediane', 'value' => $newClassMedian],
                            ['type' => 'ecarttype', 'value' => $roundedStandardDeviation],
                        ],
                    ],
                ]
            );
        }

        return new Response('Class Skill Levels mis à jour avec succès.');
    } catch (\Exception $e) {
        // Gérer l'erreur, par exemple, en la journalisant
        return new Response('Une erreur s\'est produite lors de la mise à jour des Skill Levels.');
    }
}
/**
 * @Route("/api/toggle-correction/{idExercises}", name="toggle_correction", methods={"GET"})
 */
public function toggleCorrection($idExercises)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner une collection (par exemple, "exercises")
        $collection = $database->exercises;

        // Trouver l'exercice avec l'idExercises spécifié
        $exercise = $collection->findOne(['idExercises' => (int)$idExercises]);

        if ($exercise) {
            // Inverser la valeur de correction
            $newCorrectionValue = !$exercise['correction'];

            // Mettre à jour la base de données avec la nouvelle valeur de correction
            $collection->updateOne(
                ['idExercises' => (int)$idExercises],
                ['$set' => ['correction' => $newCorrectionValue]]
            );

            // Retourner la nouvelle valeur de correction
            return new JsonResponse(['correction' => $newCorrectionValue]);
        } else {
            return new JsonResponse(['error' => 'Exercice non trouvé.'], 404);
        }
    } catch (MongoDBException $e) {
        return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la mise à jour de la correction.'], 500);
    }
}
/**
 * @Route("/api/update-time-exercises/{idExercises}", name="update_time_exercises", methods={"GET"})
 */
public function updateTimeExercises($idExercises)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner une collection (par exemple, "lexical")
        $lexicalCollection = $database->lexical;

        // Trouver l'exercice avec l'idExercises spécifié dans la collection "lexical"
        $exercise = $lexicalCollection->findOne(['idExercises' => (int)$idExercises]);

        if ($exercise) {
            // Trouver toutes les unités lexicales de l'exercice
            $lexicalUnits = $exercise['lexicalUnit'];

            // Calculer la moyenne et l'écart-type du temps
            $totalTime = 0;
            $count = 0;

            foreach ($lexicalUnits as $lexicalUnit) {
                if (isset($lexicalUnit['time'])) {
                    $totalTime += $lexicalUnit['time'];
                    $count++;
                }
            }

            $averageTime = ($count > 0) ? ($totalTime / $count) : null;

            // Calculer l'écart-type du temps
            $sumSquaredDifferences = 0;

            foreach ($lexicalUnits as $lexicalUnit) {
                if (isset($lexicalUnit['time'])) {
                    $difference = $lexicalUnit['time'] - $averageTime;
                    $sumSquaredDifferences += $difference * $difference;
                }
            }

            $standardDeviation = ($count > 0) ? sqrt($sumSquaredDifferences / $count) : null;

            // Sélectionner la collection "exercises"
            $exercisesCollection = $database->exercises;

            // Mettre à jour la base de données "exercises" avec la nouvelle moyenne et l'écart-type du temps
            $exercisesCollection->updateOne(
                ['idExercises' => (int)$idExercises],
                [
                    '$set' => [
                        'time.moy' => $averageTime,
                        'time.ecart_type' => $standardDeviation,
                    ]
                ]
            );

            // Retourner la nouvelle moyenne et l'écart-type du temps
            return new JsonResponse([
                'average_time' => $averageTime,
                'standard_deviation' => $standardDeviation
            ]);
        } else {
            return new JsonResponse(['error' => 'Exercice non trouvé.'], 404);
        }
    } catch (MongoDBException $e) {
        return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la mise à jour du temps.'], 500);
    }
}
}
