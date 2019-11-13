<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Handdifficulties;
use app\models\Evaluations;
use app\models\Lectures;
use app\models\RelatedLectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\UserLectures;
use app\models\Studenthandgoals;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;

/**
 * LekcijasController implements the actions for Lectures model by student.
 */
class LekcijasController extends Controller
{
    const VIDEOS = ['mp4','mov','ogv','webm','flv','avi','f4v'];
    const DOCS = ['doc','docx','pdf'];
    const AUDIO = ['aac','alac','amr','flac','mp3','opus','vorbis','ogg','wav'];
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
                            return Users::isStudent(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = [];
        $user = Yii::$app->user->identity;
        $modelsIds = UserLectures::getUserLectures($user->id);
        if ($modelsIds) {
            $userLectures = UserLectures::getLectures($user->id);
            $query = Lectures::find()->where(['in', 'id', $modelsIds]);
            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            $opened = UserLectures::getOpened($user->id);
            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
            $baseUrl = Yii::$app->request->baseUrl;  
            
            return $this->render('index', [
                'models' => $models,
                'userLectures' => $userLectures,
                'opened' => $opened,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS,
            ]);
        }
        
        return $this->render('index', [
            'models' => [],
            'pages' => [],
        ]);
    }

    /**
     * Displays a single Lecture model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLekcija($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        $modelsIds = UserLectures::getUserLectures($user->id);
        $check = in_array($id,$modelsIds);
        $userLectures = UserLectures::getLectures($user->id);
        if($check){
            $post = Yii::$app->request->post();
            if(isset($post['evaluations']))
            {   
                foreach($post['evaluations'] as $pid => $value){
                    $evaluation = new Userlectureevaluations();
                    $evaluation->evaluation_id = $pid;
                    $evaluation->lecture_id = $model->id;
                    $evaluation->user_id = $user->id;
                    $evaluation->created = date('Y-m-d H:i:s',time());
                    $evaluation->evaluation = $value ?? 0;
                    $evaluation->save();
                }
            }
            UserLectures::setSeenByUser($user->id,$id);            
            $difficulties = Difficulties::getDifficulties();
            $evaluations = Evaluations::getEvaluations();
            $handdifficulties = Handdifficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id); 
            $userLectureEvaluations = Userlectureevaluations::getLectureEvaluations($user->id,$id); 
            $baseUrl = Yii::$app->request->baseUrl;
            $ids = RelatedLectures::getRelations($id); 
            $relatedLectures = Lectures::getLecturesByIds($ids);     
            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                'handdifficulties' => $handdifficulties,
                'evaluations' => $evaluations,
                'lectureDifficulties' => $lectureDifficulties,
                'lectureHandDifficulties' => $lectureHandDifficulties,
                'lectureEvaluations' => $lectureEvaluations,
                'lecturefiles' => $lecturefiles,
                'userLectures' => $userLectures,
                'userLectureEvaluations' => $userLectureEvaluations,
                'videos' => self::VIDEOS,
                'docs' => self::DOCS,
                'audio' => self::AUDIO,
                'baseUrl' => $baseUrl,
                'relatedLectures' => $relatedLectures
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Lectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
