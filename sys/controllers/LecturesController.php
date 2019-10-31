<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Lectures;
use app\models\LecturesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
     * Lists all Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LecturesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        $admins = Users::getAdmins(); 
        $complexity = Lectures::getComplexity();
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
        $model = new Lectures();
        $model->author = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s',time());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
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
        $model = $this->findModel($id);
        $model->updated = date('Y-m-d H:i:s',time());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);        
            //return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
             'model' => $model,
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
