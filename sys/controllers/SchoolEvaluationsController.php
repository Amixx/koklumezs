<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\School;
use app\models\SchoolEvaluations;
use app\models\SchoolTeacher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class SchoolEvaluationsController extends Controller
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

    public function actionIndex()
    {
        $schoolId = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id)->school_id;
        $dataProvider = new ActiveDataProvider([
            'query' => SchoolEvaluations::getForSchool($schoolId),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new SchoolEvaluations();

        if ($model->load(Yii::$app->request->post())) {
            $model->school_id = $school['id'];
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($post) {
            $model->load($post);
            $model->stars = isset($post["SchoolEvaluations"]["stars"]) ? $post["SchoolEvaluations"]["stars"] : null;
            $model->is_scale = isset($post["SchoolEvaluations"]["is_scale"]) ? $post["SchoolEvaluations"]["is_scale"] : null;
            $model->is_video_param = isset($post["SchoolEvaluations"]["is_video_param"]) ? $post["SchoolEvaluations"]["is_video_param"] : null;
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

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SchoolEvaluations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
