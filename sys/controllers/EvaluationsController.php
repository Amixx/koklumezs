<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Evaluations;
use app\models\EvaluationsSearch;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EvaluationsController implements the CRUD actions for Evaluations model.
 */
class EvaluationsController extends Controller
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->username);
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
     * Lists all Evaluations models.
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
        $searchModel = new EvaluationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' =>  $get
        ]);
    }

    /**
     * Displays a single Evaluations model.
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
     * Creates a new Evaluations model.
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
        $model = new Evaluations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Evaluations model.
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
        // $E = array("Viss tik viegls, ka garlaicīgi","Ļoti ļoti viegli, noteikti vajag grūtāk","Izspēlēju vienu reizi un jau viss skaidrs","Diezgan vienkārši","Nācās pastrādāt, bet tiku galā bez milzīgas piepūles","Tiku galā","Diezgan grūti","Itkā saprotu, bet pirksti neklausa","Kaut ko mēģinu, bet pārāk nesanāk","Vispār neko nesaprotu");
        // $s = serialize($E);
        // var_dump($s);die;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($post) {
            $model->load($post);
            $model->stars = isset($post["Evaluations"]["stars"]) ? $post["Evaluations"]["stars"] : null;
            $model->is_scale = isset($post["Evaluations"]["is_scale"]) ? $post["Evaluations"]["is_scale"] : null;
            $model->is_video_param = isset($post["Evaluations"]["is_video_param"]) ? $post["Evaluations"]["is_video_param"] : null;
            $model->star_text = isset($post['stars_texts']) ? serialize($post['stars_texts']) : null;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $stars_texts = unserialize($model->star_text);

        return $this->render('update', [
            'model' => $model,
            'stars_texts' => $stars_texts
        ]);
    }

    /**
     * Deletes an existing Evaluations model.
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
     * Finds the Evaluations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Evaluations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Evaluations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
