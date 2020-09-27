<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Evaluations;
use app\models\LectureAssignment;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\School;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\SectionsVisible;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LekcijasController implements the actions for Lectures model by student.
 */
class FileController extends Controller
{
    const DOCS = ['doc', 'docx', 'pdf'];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return !empty(Yii::$app->user->identity); //Users::isStudent(Yii::$app->user->identity->username);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }


    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $user = Yii::$app->user->identity;
        $force = Yii::$app->request->get('force');
        $userLectureIds =  UserLectures::getUserLectures($user->id);
        $userLectureFiles = [];
        foreach ($userLectureIds as $id) {
            $anyLectureFiles = count(Lecturesfiles::getLectureFiles($id)) > 0;
            $isAlreadyAdded = false;
            foreach ($userLectureFiles as $file) {
                if ($anyLectureFiles && $file['title'] == Lecturesfiles::getLectureFiles($id)[0]['title']) {
                    $isAlreadyAdded = true;
                }
            };
            if ($anyLectureFiles && !$isAlreadyAdded) {
                array_push($userLectureFiles, Lecturesfiles::getLectureFiles($id)[0]);
            }
        }
        $alphabet = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "ā", "b", "c", "č", "d", "e", "ē", "f", "g", "ģ", "h", "i", "ī", "j", "k", "ķ", "l", "ļ", "m", "n", "ņ", "o", "p", "q", "r", "s", "š", "t", "u", "ū", "v", "w", "x", "y", "z", "ž");

        usort($userLectureFiles, function ($a, $b) use ($alphabet) {
            $aTitle = mb_strtolower(trim($a['title']), 'UTF-8');
            $bTitle = mb_strtolower(trim($b['title']), 'UTF-8');

            $aFirst = mb_substr($aTitle, 0, 1);
            $bFirst = mb_substr(
                $bTitle,
                0,
                1
            );

            $aIndex = count(array_keys($alphabet, $aFirst)) == 1 ? array_keys($alphabet, $aFirst)[0] : array_keys($alphabet, $aFirst)[1];
            $bIndex = count(array_keys($alphabet, $bFirst)) == 1 ? array_keys($alphabet, $bFirst)[0] : array_keys($alphabet, $bFirst)[1];

            return $aIndex > $bIndex;
        });
        return $this->render('index', [
            'lecturefiles' => $userLectureFiles,
            'docs' => self::DOCS,
            'force' => $force
        ]);
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
