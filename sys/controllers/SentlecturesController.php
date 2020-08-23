<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Lectures;
use app\models\Sentlectures;
use app\models\SentlecturesSearch;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SentlecturesController implements the CRUD actions for Sentlectures model.
 */
class SentlecturesController extends Controller
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sentlectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $searchModel = new SentlecturesSearch();
        $students = Users::getActiveStudents();
        $lectures = Lectures::getLectures();
        $get = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($get);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'lectures' => $lectures,
            'students' => $students,
            'get' => $get,
        ]);
    }

    /**
     * Displays a single Sentlectures model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sentlectures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new Sentlectures();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sentlectures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sentlectures model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sentlectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sentlectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sentlectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
