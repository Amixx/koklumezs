<?php

namespace app\controllers;

use Yii;
use app\models\Userlectureevaluations;
use app\models\UserlectureevaluationsSearch;
use app\models\TeacherUserlectureevaluationsSearch;
use app\models\Users;
use app\models\Lectures;
use app\models\Evaluations;
use app\models\CommentResponses;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserLectureEvaluationsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Userlectureevaluations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userContext = Yii::$app->user->identity;
        $isTeacher = $userContext->isTeacher();
        $searchModel = $isTeacher ? new TeacherUserlectureevaluationsSearch() : new UserlectureevaluationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);
        $students = Users::getActiveStudentEmails();
        $lectures = Lectures::getLectures();
        $evaluations = Evaluations::getEvaluationsTitles();
        if (Yii::$app->language == 'lv') {
            $evaluations = array_map(function ($eval) {
                return Yii::t('app',  $eval);
            }, $evaluations);
        }
        $get = Yii::$app->request->queryParams;
        $commentResponsesDataProvider = CommentResponses::getAllCommentResponses();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'lectures' => $lectures,
            'evaluations' => $evaluations,
            'get' => $get,
            'commentResponsesDataProvider' => $commentResponsesDataProvider,
            'isTeacher' => $isTeacher,
        ]);
    }

    public function actionComments()
    {
        $userContext = Yii::$app->user->identity;
        $isTeacher = $userContext->isTeacher();
        $searchModel = $isTeacher ? new TeacherUserlectureevaluationsSearch() : new UserlectureevaluationsSearch();
        $get = Yii::$app->request->queryParams;
        $get["UserlectureevaluationsSearch"]["evaluation_id"] = 4;
        $dataProvider = $searchModel->search($get, true);
        $students = Users::getActiveStudentEmails();
        $lectures = Lectures::getLectures();
        $evaluations = Evaluations::getEvaluationsTitles();


        return $this->render('comments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'lectures' => $lectures,
            'evaluations' => $evaluations,
            'get' => $get,
        ]);
    }

    /**
     * Displays a single Userlectureevaluations model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Userlectureevaluations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Userlectureevaluations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Userlectureevaluations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Userlectureevaluations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Userlectureevaluations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Userlectureevaluations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Userlectureevaluations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
