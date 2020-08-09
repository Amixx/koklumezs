<?php

namespace app\controllers;

use Yii;
use app\models\Userlectureevaluations;
use app\models\UserlectureevaluationsSearch;
use app\models\Users;
use app\models\Lectures;
use app\models\Evaluations;
use app\models\CommentResponses;
use app\models\CommentresponsesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserLectureEvaluationsController implements the CRUD actions for Userlectureevaluations model.
 */
class UserLectureEvaluationsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
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
        $searchModel = new UserlectureevaluationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $students = Users::getActiveStudents();
        $lectures = Lectures::getLectures();
        $evaluations = Evaluations::getEvaluationsTitles();
        $get = Yii::$app->request->queryParams;

        // $commentResponsesSearchModel = new CommentresponsesSearch();
        // $commentResponsesDataProvider = $commentResponsesSearchModel->search(Yii::$app->request->queryParams);
        $commentResponsesDataProvider = CommentResponses::getAllCommentResponses();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'lectures' => $lectures,
            'evaluations' => $evaluations,
            'get' => $get,
            'commentResponsesDataProvider' => $commentResponsesDataProvider
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
