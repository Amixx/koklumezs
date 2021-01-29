<?php

namespace app\controllers;

use yii\data\ActiveDataProvider;
use app\models\Difficulties;
use app\models\Evaluations;
use app\models\LectureAssignment;
use app\models\Lectures;
use app\models\PlanFiles;
use app\models\SchoolSubplanParts;
use app\models\PlanParts;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\SchoolSubPlans;
use app\models\StudentSubplanPauses;
use app\models\SectionsVisible;
use app\models\School;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ArchiveController implements the actions for Lectures model by student.
 */
class SchoolSubPlansController extends Controller
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $dataProvider = new ActiveDataProvider([
            'query' => SchoolSubPlans::getForCurrentSchool(),
        ]);
        $pausesDataProvider = new ActiveDataProvider([
            'query' => StudentSubplanPauses::getForCurrentSchool(),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pausesDataProvider' => $pausesDataProvider,
        ]);
    }

    public function actionView($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $model =  $this->findModel($id);

        $planFiles = new ActiveDataProvider([
            'query' => PlanFiles::getFilesForPlan($model->id),
        ]);
        $planParts = new ActiveDataProvider([
            'query' => SchoolSubplanParts::getForSchoolSubplan($model->id),
        ]);
        $planTotalCost = SchoolSubplanParts::getPlanTotalCost($model->id);

        return $this->render('view', [
            'model' => $model,
            'planFiles' => $planFiles,
            'planParts' => $planParts,
            'planTotalCost' => $planTotalCost,
        ]);
    }

    public function actionCreate()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $model = new SchoolSubPlans();
        $schoolId = School::getCurrentSchoolId();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model->school_id = $schoolId;
            if ($model->save()) {
                if(isset($post["file-title"]) && isset($post["file"])){
                    $planFile = new PlanFiles();
                    $planFile->plan_id = $model->id;
                    $planFile->title = $post["file-title"];
                    $planFile->file = $post["file"];
                    $planFile->save();
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $model = $this->findModel($id);
        $schoolId = School::getCurrentSchoolId();
        $planFiles = new ActiveDataProvider([
            'query' => PlanFiles::getFilesForPlan($model->id),
        ]);
        $subplanParts = new ActiveDataProvider([
            'query' => SchoolSubplanParts::getForSchoolSubplan($model->id),
        ]);
        $schoolSubplanParts = PlanParts::getForSchool($schoolId);
        $newSubplanPart = new SchoolSubplanParts;
        $post = Yii::$app->request->post();

        $saved = $model->load($post) && $model->save();
        if(isset($post["file-title"]) && isset($post["file"])){
            $planFile = new PlanFiles();
            $planFile->plan_id = $model->id;
            $planFile->title = $post["file-title"];
            $planFile->file = $post["file"];
            $planFile->save();
        }
        if($post && isset($post['SchoolSubplanParts']) && isset($post['SchoolSubplanParts']['planpart_id']) && $post['SchoolSubplanParts']['planpart_id']){
            $newSubplanPart->schoolsubplan_id = $model->id;
            $newSubplanPart->planpart_id = (int) $post['SchoolSubplanParts']['planpart_id'];

            if($newSubplanPart->save()){
                Yii::$app->session->setFlash('success', 'Plāna daļa pievienta!');
                $newSubplanPart = new SchoolSubplanParts;
            }             
        }

        if($saved) return $this->redirect(['view', 'id' => $model->id]);

        
        return $this->render('update', [
            'model' => $model,
            'planFiles' => $planFiles,
            'subplanParts' => $subplanParts,
            'newSubplanPart' => $newSubplanPart,
            'schoolSubplanParts' => $schoolSubplanParts,
        ]);
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SchoolSubPlans::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
