<?php

namespace app\fitness\controllers;

use app\fitness\models\Exercise;
use Yii;
use app\fitness\models\WeightExerciseAbilityRatio;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class WeightExerciseAbilityRatioController extends Controller
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

    public function actionIndex()
    {
//        $tests = [
//            Exercise::find()->where(['id' => 383])->one(),
//            Exercise::find()->where(['id' => 1569])->one(),
//            Exercise::find()->where(['id' => 1570])->one(),
//            Exercise::find()->where(['id' => 1571])->one(),
//        ];
//        foreach($tests as $test) {
//            $x = $test->findBodyweightExerciseChainMainExercise();
//            var_dump($x);
//            echo "<hr/>";
//            echo "<hr/>";
//            echo "<hr/>";
//        }
//        die();

        $userContext = Yii::$app->user->identity;
        $dataProvider = new ActiveDataProvider([
            'query' => WeightExerciseAbilityRatio::find()
                ->joinWith('exercise1')
                ->joinWith('exercise2')
                ->where(['author_id' => $userContext->id]),
        ]);

        return $this->render('@app/fitness/views/weight-exercise-ability-ratio/index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new WeightExerciseAbilityRatio;

        if ($model->load($post)) {
            if($model->save()) {
                return $this->redirect('index');
            }
        }

        return $this->render('@app/fitness/views/weight-exercise-ability-ratio/create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::to('index'));
        }

        return $this->render('@app/fitness/views/weight-exercise-ability-ratio/update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Weight exercise ability ratio deleted!'));

        return $this->redirect('index');
    }

    protected function findModel($id)
    {
        if (($model = WeightExerciseAbilityRatio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
