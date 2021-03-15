<?php

namespace app\controllers;

use app\models\Lecturesfiles;
use app\models\UserLectures;
use app\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{
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
                            return !empty(Yii::$app->user->identity);
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
        $user = Yii::$app->user->identity;
        $force = Yii::$app->request->get('force');
        $userLectureIds =  UserLectures::getUserLectures($user->id);
        $userLectureFiles = [];
        $note_filter = Yii::$app->request->get('note_filter');

        foreach ($userLectureIds as $id) {
            $lectureFiles = Lecturesfiles::getLectureFiles($id);

            $isAlreadyAdded = false;
            foreach ($userLectureFiles as $file) {
                foreach($lectureFiles['docs'] as $lectureFile){
                    if ($file['title'] == $lectureFile['title']) {
                        $isAlreadyAdded = true;
                        break;
                    }
                }
            }

            if (!$isAlreadyAdded) {
                foreach($lectureFiles['docs'] as $file){
                    array_push($userLectureFiles, $file);
                }   
            }         
        }

        if ($note_filter) {
            $userLectureFiles = array_filter($userLectureFiles, function ($item) use ($note_filter) {
                $title_lower = mb_strtolower(trim($item['title']), 'UTF-8');
                return strpos($title_lower, $note_filter) !== false;
            });
        }

        $alphabet = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "ā", "b", "c", "č", "d", "e", "ē", "f", "g", "ģ", "h", "i", "ī", "j", "k", "ķ", "l", "ļ", "m", "n", "ņ", "o", "p", "q", "r", "s", "š", "t", "u", "ū", "v", "w", "x", "y", "z", "ž");

        usort($userLectureFiles, function ($a, $b) use ($alphabet) {
            $aTitle = mb_strtolower(trim($a['title']), 'UTF-8');
            $bTitle = mb_strtolower(trim($b['title']), 'UTF-8');

            $aFirst = mb_substr($aTitle, 0, 1);
            $bFirst = mb_substr($bTitle, 0, 1);

            $aIndex = count(array_keys($alphabet, $aFirst)) == 1 ? array_keys($alphabet, $aFirst)[0] : array_keys($alphabet, $aFirst)[1];
            $bIndex = count(array_keys($alphabet, $bFirst)) == 1 ? array_keys($alphabet, $bFirst)[0] : array_keys($alphabet, $bFirst)[1];

            return $aIndex > $bIndex;
        });

        return $this->render('index', [
            'lecturefiles' => $userLectureFiles,
            'note_filter' => $note_filter,
            'force' => $force
        ]);
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
