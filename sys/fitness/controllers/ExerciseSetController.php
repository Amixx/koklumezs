<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\ExerciseSet;
use app\models\Users;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExerciseSetController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate($exercise_id)
    {
        $post = Yii::$app->request->post();
        $model = new ExerciseSet;
        $model->author_id = Yii::$app->user->identity->id;
        $model->exercise_id = $exercise_id;

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::to(['fitness-exercises/view', 'id' => $exercise_id]));
        }

        return $this->render('@app/fitness/views/exercise-set/create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::previous());
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/exercise-set/update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id, $exercise_id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Exercise set deleted!'));

        return $this->redirect(Url::to(['fitness-exercises/view', 'id' => $exercise_id]));
    }

    public function actionApiList()
    {
        $exerciseSets = ExerciseSet::find()->asArray()->all();
        return json_encode($exerciseSets);
    }

    protected function findModel($id)
    {
        if (($model = ExerciseSet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
