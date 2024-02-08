<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception as MongoDBException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
class LexicalController extends AbstractController
{
/**
 * @Route("/api/get-lexical-data/{idExercises}", name="get_lexical_data", methods={"GET"})
 */
public function getLexicalData($idExercises)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner une collection (par exemple, "lexical")
        $collection = $database->lexical;

        // Exécuter l'agrégation
        $pipeline = [
            ['$match' => ['idExercises' => (int) $idExercises]],
            ['$group' => [
                '_id' => '$idLexical',
                'totalLexicalUnitIds' => ['$sum' => ['$size' => '$lexicalUnit']],
            ]],
        ];

        $result = $collection->aggregate($pipeline)->toArray();

        // Calculer la somme totale des lexicalUnitIds
        $totalUnits = 0;
        foreach ($result as $entry) {
            $totalUnits += $entry['totalLexicalUnitIds'];
        }

        // Construire la réponse JSON
        $jsonResponse = json_encode([
            'idExercises' => (int) $idExercises,
            'NbIdLexical' => count($result),
            'TotalLexicalCount' => $totalUnits,
        ], JSON_UNESCAPED_UNICODE);

        // Retourner une réponse JSON réussie
        return new Response($jsonResponse, 200, ['Content-Type' => 'application/json']);
    } catch (MongoDBException $e) {
        // Gérer l'erreur, par exemple, en la journalisant
        return new Response('Une erreur s\'est produite lors de la récupération des données. Détails : ' . $e->getMessage());
    }
}
 /**
 * @Route("/api/get-all-text/{idExercises}", name="get_all_text", methods={"GET"})
 */
public function getAllText($idExercises): Response
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection "lexical"
        $collection = $database->lexical;

        // Exécuter l'agrégation pour obtenir tous les textes de l'exercice spécifié
        $pipeline = [
            ['$match' => ['idExercises' => (int) $idExercises]],
            ['$unwind' => '$text'],
            ['$group' => ['_id' => null, 'allText' => ['$push' => '$text']]],
        ];

        $result = $collection->aggregate($pipeline)->toArray();

        // Vérifier si aucun résultat n'a été trouvé
        if (empty($result)) {
            return new Response('Aucun texte trouvé pour cet exercice.', 404);
        }

  // Convertir le BSONArray en un tableau PHP standard
        $allTextArray = iterator_to_array($result[0]['allText']);

        // Concaténer tous les textes en un seul
        $allText = implode(' ', $allTextArray);

        // Calculer le temps de lecture, le temps de lecture oral, la longueur moyenne des mots, 
        // la longueur moyenne des phrases et le nombre total de caractères
        $readingTime = $this->calculateReadingTime($allText);
        $oralReadingTime = $this->calculateReadingOral($allText);
        $wordLength = $this->calculateWordLength($allText);
        $sentenceLength = $this->calculateSentenceLength($allText);
        $nbCaractere = $this->calculateNbCaractere($allText);

        // Retourner les informations calculées sous forme de tableau JSON
        $responseData = [
            'allText' => $allText,
            'readingTime' => $readingTime,
            'oralReadingTime' => $oralReadingTime,
            'wordLength' => $wordLength,
            'sentenceLength' => $sentenceLength,
            'nbCaractere' => $nbCaractere,
        ];

        return new JsonResponse($responseData);

    } catch (\Exception $e) {
        // Gérer l'erreur, par exemple, en la journalisant
        return new Response('Une erreur s\'est produite lors de la récupération des données. Détails : ' . $e->getMessage(), 500);
    }
}
 private function calculateReadingTime($text) {
        $wordsPerMinute = 175;
        $wordCount = str_word_count($text);
        $minutes = $wordCount / $wordsPerMinute;
        $seconds = $minutes * 60;
        return ceil($seconds);
    }

    private function calculateReadingOral($text) {
        $wordsPerMinute = 150;
        $wordCount = str_word_count($text);
        $minutes = $wordCount / $wordsPerMinute;
        $seconds = $minutes * 60;
        return ceil($seconds);
    }

    private function calculateWordLength($text) {
        $words = str_word_count($text, 1);
        $totalLetters = array_sum(array_map('strlen', $words));
        $averageSize = $totalLetters / count($words);
        return round($averageSize, 1);
    }

    private function calculateSentenceLength($text) {
        $sentences = preg_split('/[.?!;]/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $totalWords = str_word_count($text);
        $averageSize = $totalWords / count($sentences);
        return round($averageSize, 1);
    }

    private function calculateNbCaractere($text) {
        return mb_strlen($text);
    }

 /**
 * @Route("/api/recurrent-errors/{idStudent}", name="get_recurrent_errors", methods={"GET"})
 */
public function getRecurrentErrors($idStudent)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection "students"
        $studentsCollection = $database->students;

        // Trouver l'étudiant avec l'id spécifié
        $student = $studentsCollection->findOne(['idStudent' => (int)$idStudent]);

        if (!$student) {
            return new JsonResponse(['error' => 'Étudiant non trouvé.'], 404);
        }

        // Initialiser le tableau RecurrentError
        $recurrentError = [];

        // Récupérer les idExercises de l'étudiant
        $idExercises = $student['idExercises'];

        // Pour chaque idExercises de l'étudiant
        foreach ($idExercises as $idExercise) {
            // Trouver l'exercice correspondant dans la collection "lexical"
            $exercise = $database->lexical->findOne(['idExercises' => (int)$idExercise]);

            // Vérifier si l'exercice existe
            if ($exercise) {
                // Récupérer les unités lexicales de l'exercice
                $lexicalUnits = $exercise['lexicalUnit'];

                // Pour chaque unité lexicale, compter les erreurs par POS
                foreach ($lexicalUnits as $lexicalUnit) {
                    if (isset($lexicalUnit['error']) && $lexicalUnit['error'] === true) {
                        $pos = $lexicalUnit['pos'];
                        // Ajouter l'erreur à la catégorie correspondante dans RecurrentError
                        if (!isset($recurrentError[$pos])) {
                            $recurrentError[$pos] = 0;
                        }
                        $recurrentError[$pos]++;
                    }
                }
            }
        }

        // Formater le tableau RecurrentError selon votre structure spécifiée
        $formattedRecurrentError = [
            'Grammaire' => [],
            'Conjugaison' => [],
            'Ponctuation' => []
        ];

        foreach ($recurrentError as $pos => $count) {
            if (in_array($pos, ["ADJ", "ADP", "ADV", "CCONJ", "DET", "NOUN", "NUM", "PART", "PRON", "PROPN", "SCONJ"])) {
                $formattedRecurrentError['Grammaire'][] = ['posDetails' => $pos, 'count' => $count];
            } elseif (in_array($pos, ["AUX", "VERB"])) {
                $formattedRecurrentError['Conjugaison'][] = ['posDetails' => $pos, 'count' => $count];
            } elseif (in_array($pos, ["INTJ", "PUNCT", "SYM"])) {
                $formattedRecurrentError['Ponctuation'][] = ['posDetails' => $pos, 'count' => $count];
            }
        }

        // Stockage des résultats dans le champ studentsRecurrentError du document de l'étudiant
        $updateResult = $studentsCollection->updateOne(
            ['idStudent' => (int)$idStudent],
            ['$set' => ['studentsRecurrentError' => $formattedRecurrentError]]
        );

        if ($updateResult->getModifiedCount() === 1) {
            // Succès : les résultats ont été stockés dans la base de données
            return new JsonResponse(['message' => 'Erreurs récurrentes mises à jour pour l\'étudiant avec succès']);
        } else {
            // Échec : les résultats n'ont pas été mis à jour
            return new JsonResponse(['error' => 'Impossible de mettre à jour les erreurs récurrentes pour l\'étudiant'], 500);
        }

    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la récupération des erreurs récurrentes.'], 500);
    }
}

/**
 * @Route("/api/class-recurrent-errors/{idClass}", name="get_class_recurrent_errors", methods={"GET"})
 */
public function getClassRecurrentErrors($idClass)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection "class" pour obtenir les étudiants de la classe spécifiée
        $classCollection = $database->class;

        // Trouver la classe avec l'id spécifié
        $class = $classCollection->findOne(['idClass' => (int)$idClass]);

        if (!$class) {
            return new JsonResponse(['error' => 'Classe non trouvée.'], 404);
        }

        // Initialiser le tableau classRecurrentError
        $classRecurrentError = [];

        // Récupérer les idStudent de la classe
        $studentsIds = $class['studentOfClassById'];

        // Sélectionner la collection "students"
        $studentsCollection = $database->students;

        // Pour chaque idStudent de la classe
        foreach ($studentsIds as $studentId) {
            // Trouver l'étudiant correspondant dans la collection "students"
            $student = $studentsCollection->findOne(['idStudent' => (int)$studentId]);

            if ($student && isset($student['studentsRecurrentError'])) {
                // Agréger les erreurs récurrentes de l'étudiant dans le tableau classRecurrentError
                foreach ($student['studentsRecurrentError'] as $category => $errors) {
                    foreach ($errors as $error) {
                        $posDetails = $error['posDetails'];
                        $count = $error['count'];
                        if (!isset($classRecurrentError[$category][$posDetails])) {
                            $classRecurrentError[$category][$posDetails] = 0;
                        }
                        $classRecurrentError[$category][$posDetails] += $count;
                    }
                }
            }
        }

        $updateResult = $classCollection->updateOne(
    ['idClass' => (int)$idClass],
    ['$set' => ['classRecurrentError' => $classRecurrentError]]
);

// Vérifier si la mise à jour a été effectuée avec succès
if ($updateResult->getModifiedCount() === 1) {
    return new JsonResponse(['success' => 'Les erreurs récurrentes de la classe ont été mises à jour avec succès.']);
} else {
    return new JsonResponse(['error' => 'La mise à jour des erreurs récurrentes de la classe a échoué.']);
}

    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la récupération des erreurs récurrentes de la classe.'], 500);
    }
}

/**
 * @Route("/api/student-aggregation/{idStudent}", name="student_aggregation", methods={"GET"})
 */
public function getStudentAggregation($idStudent)
{
    try {
        // Créer une instance du client MongoDB
        $mongoClient = new \MongoDB\Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection "students"
        $studentsCollection = $database->students;

        // Agrégation pour compter les erreurs par POS
        $pipeline = [
            [
                '$match' => ['idStudent' => (int)$idStudent]
            ],
            [
                '$lookup' => [
                    'from' => 'lexical',
                    'localField' => 'idExercises',
                    'foreignField' => 'idExercises',
                    'as' => 'exercises'
                ]
            ],
            [
                '$unwind' => '$exercises'
            ],
            [
                '$unwind' => '$exercises.lexicalUnit'
            ],
            [
                '$group' => [
                    '_id' => '$exercises.lexicalUnit.pos',
                    'count' => ['$sum' => 1]
                ]
            ]
        ];

        $aggregationResult = $studentsCollection->aggregate($pipeline);

        // Formater le résultat de l'agrégation
        $formattedResult = [];
        foreach ($aggregationResult as $item) {
            $formattedResult[$item['_id']] = $item['count'];
        }

        $updateResult = $studentsCollection->updateOne(
    ['idStudent' => (int)$idStudent],
    ['$set' => ['RecurrentPosUse' => $formattedResult]]
);

if ($updateResult->getModifiedCount() === 1) {
    // Succès : les résultats ont été stockés dans la base de données
    return new JsonResponse(['message' => 'Données agrégées stockées avec succès dans le tableau RecurrentPosUse[] pour l\'étudiant']);
} else {
    // Échec : les résultats n'ont pas été mis à jour
    return new JsonResponse(['error' => 'Impossible de mettre à jour les données agrégées pour l\'étudiant'], 500);
}

    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
/**
 * @Route("/api/class-recurrent-pos-use/{idClass}", name="get_class_recurrent_pos_use", methods={"GET"})
 */
public function getClassRecurrentPosUse($idClass)
{
try {
        // Créer une instance du client MongoDB
        $mongoClient = new Client('mongodb://mongo:27017');

        // Sélectionner la base de données
        $database = $mongoClient->mydatabase;

        // Sélectionner la collection "class" pour obtenir les étudiants de la classe spécifiée
        $classCollection = $database->class;

        // Trouver la classe avec l'id spécifié
        $class = $classCollection->findOne(['idClass' => (int)$idClass]);

        if (!$class) {
            return new JsonResponse(['error' => 'Classe non trouvée.'], 404);
        }

        // Initialiser le tableau classRecurrentPosUse
        $classRecurrentPosUse = [];

        // Récupérer les idStudent de la classe
        $studentsIds = $class['studentOfClassById'];

        // Sélectionner la collection "students"
        $studentsCollection = $database->students;

        // Pour chaque idStudent de la classe
        foreach ($studentsIds as $studentId) {
            // Trouver l'étudiant correspondant dans la collection "students"
            $student = $studentsCollection->findOne(['idStudent' => (int)$studentId]);

            if ($student && isset($student['RecurrentPosUse'])) {
                // Agréger les RecurrentPosUse de l'étudiant dans le tableau classRecurrentPosUse
                foreach ($student['RecurrentPosUse'] as $pos => $count) {
                    if (!isset($classRecurrentPosUse[$pos])) {
                        $classRecurrentPosUse[$pos] = 0;
                    }
                    $classRecurrentPosUse[$pos] += $count;
                }
            }
        }

        // Mettre à jour la classe avec les données agrégées
        $updateResult = $classCollection->updateOne(
            ['idClass' => (int)$idClass],
            ['$set' => ['ClassRecurrentPosUse' => $classRecurrentPosUse]]
        );

        // Vérifier si la mise à jour a été effectuée avec succès
        if ($updateResult->getModifiedCount() === 1) {
            return new JsonResponse(['success' => 'Les RecurrentPosUse de la classe ont été mis à jour avec succès.']);
        } else {
            return new JsonResponse(['error' => 'La mise à jour des RecurrentPosUse de la classe a échoué.']);
        }
    } catch (\Exception $e) {
        // Afficher le message d'erreur complet
        return new JsonResponse(['error' => $e->getMessage()], 500);
    }
}


}
