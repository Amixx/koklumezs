<?php

namespace app\controllers;

use app\models\Lectures;
use app\models\UserLectures;
use app\models\Users;
use app\models\School;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ArchiveController extends Controller
{
    const VIDEOS = ['mp4', 'mov', 'ogv', 'webm', 'flv', 'avi', 'f4v'];
    const DOCS = ['doc', 'docx', 'pdf'];
    const AUDIO = ['aac', 'alac', 'amr', 'flac', 'mp3', 'opus', 'vorbis', 'ogg', 'wav'];

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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $archive = [];
        $user = Yii::$app->user->identity;
        $evaluatedIds = UserLectures::getEvaluatedUserLectures($user->id);
        if ($evaluatedIds) {
            $archive_filter = Yii::$app->request->get('archive_filter');
            $archive = Lectures::find()->where(['in', 'id', $evaluatedIds])->all();

            if ($archive_filter) {
                $archive = array_filter($archive, function ($item) use ($archive_filter) {
                    $title_lower = mb_strtolower(trim($item->title), 'UTF-8');
                    return strpos($title_lower, $archive_filter) !== false;
                });
            }
            $alphabet = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "ā", "b", "c", "č", "d", "e", "ē", "f", "g", "ģ", "h", "i", "ī", "j", "k", "ķ", "l", "ļ", "m", "n", "ņ", "o", "p", "q", "r", "s", "š", "t", "u", "ū", "v", "w", "x", "y", "z", "ž");

            usort($archive, function ($a, $b) use ($alphabet) {
                $aTitle = mb_strtolower(trim($a->title), 'UTF-8');
                $bTitle = mb_strtolower(trim($b->title), 'UTF-8');

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
            $baseUrl = Yii::$app->request->baseUrl;

            $videoThumb = School::getCurrentSchool()->video_thumbnail;

            return $this->render('index', [
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS,
                'archive' => $archive,
                'archive_filter' => $archive_filter,
                'videoThumb' => $videoThumb
            ]);
        }

        return $this->render('index', [
            'archive' => $archive
        ]);
    }
}
