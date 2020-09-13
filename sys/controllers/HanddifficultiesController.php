<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Handdifficulties;
use app\models\HanddifficultiesSearch;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanddifficultiesController implements the CRUD actions for Handdifficulties model.
 */
class HanddifficultiesController extends Controller
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
                            return Users::isUserAdmin(Yii::$app->user->identity->username);
                        }
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
     * Lists all Handdifficulties models.
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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $searchModel = new HanddifficultiesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get
        ]);
    }

    /**
     * Displays a single Handdifficulties model.
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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Handdifficulties model.
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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new Handdifficulties();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Handdifficulties model.
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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
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
     * Deletes an existing Handdifficulties model.
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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Handdifficulties model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Handdifficulties the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Handdifficulties::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
