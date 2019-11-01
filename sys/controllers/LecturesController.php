<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Handdifficulties;
use app\models\LecturesDifficulties;
use app\models\Lectureshanddifficulties;
use app\models\Lectures;
use app\models\LecturesSearch;
use app\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LecturesController implements the CRUD actions for Lectures model.
 */
class LecturesController extends Controller
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
                            return Users::isUserAdmin(Yii::$app->user->identity->email);
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
     * Lists all Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LecturesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        $admins = Users::getAdmins();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get,
            'admins' => $admins,
            
        ]);
    }

    /**
     * Displays a single Lectures model.
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
     * Creates a new Lectures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $post = Yii::$app->request->post();
        $model = new Lectures();
        $model->author = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s', time());
        if ($model->load($post) && $model->save()) {
            if($post['difficulties'])
            {   
                foreach($post['difficulties'] as $id => $value){
                    $difficulty = new LecturesDifficulties();
                    $difficulty->diff_id = $id;
                    $difficulty->lecture_id = $model->id;
                    $difficulty->value = $value ?? 0;
                    $difficulty->save();
                }
            }
            if($post['handdifficulties'])
            {   
                foreach($post['handdifficulties'] as $id => $value){
                    $handdifficulty = new Lectureshanddifficulties();
                    $handdifficulty->category_id = $id;
                    $handdifficulty->lecture_id = $model->id;
                    $handdifficulty->save();
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties
        ]);
    }

    /**
     * Updates an existing Lectures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
        $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
        $model = $this->findModel($id);
        $model->updated = date('Y-m-d H:i:s', time());
        if ($model->load($post) && $model->save()) {
            if($post['difficulties'])
            {   
                LecturesDifficulties::removeLectureDifficulties($id);
                foreach($post['difficulties'] as $pid => $value){
                    $difficulty = new LecturesDifficulties();
                    $difficulty->diff_id = $pid;
                    $difficulty->lecture_id = $model->id;
                    $difficulty->value = $value ?? 0;
                    $difficulty->save();
                }
            }
            if($post['handdifficulties'])
            {   
                Lectureshanddifficulties::removeLectureDifficulties($id);
                foreach($post['handdifficulties'] as $pid => $value){
                    $handdifficulty = new Lectureshanddifficulties();
                    $handdifficulty->category_id = $pid;
                    $handdifficulty->lecture_id = $model->id;
                    $handdifficulty->save();
                }
            }
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties,
            'lectureDifficulties' => $lectureDifficulties,
            'lectureHandDifficulties' => $lectureHandDifficulties,
        ]);
    }

    /**
     * Deletes an existing Lectures model.
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
