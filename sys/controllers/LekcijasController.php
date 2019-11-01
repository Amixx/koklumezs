<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Evaluations;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\UserLectures;
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
            $query = Lectures::find()->where(['in', 'id', $modelsIds]);
            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            return $this->render('index', [
                'models' => $models,
                'pages' => $pages,
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
        $difficulties = Difficulties::getDifficulties();
        $evaluations = Evaluations::getEvaluations();
        $handdifficulties = Handdifficulties::getDifficulties();
        $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
        $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
        $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
        $lecturefiles = Lecturesfiles::getLectureFiles($id);
        $model = $this->findModel($id);
        return $this->render('lekcija', [
            'model' => $model,
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties,
            'evaluations' => $evaluations,
            'lectureDifficulties' => $lectureDifficulties,
            'lectureHandDifficulties' => $lectureHandDifficulties,
            'lectureEvaluations' => $lectureEvaluations,
            'lecturefiles' => $lecturefiles,
        ]);
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
