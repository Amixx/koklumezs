<?php

namespace app\fitness\controllers;

use app\fitness\models\Exercise;
use app\fitness\models\ProgressionChainExercise;
use Yii;
use app\fitness\models\ProgressionChain;
use app\models\Users;
use app\fitness\models\TagSearch;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProgressionChainController extends Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => ProgressionChain::find(),
        ]);

        return $this->render('@app/fitness/views/progression-chain/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = ProgressionChain::find()->where(['fitness_progression_chains.id' => $id])->joinWith('exercises')->one();
        return $this->render('@app/fitness/views/progression-chain/view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new ProgressionChain();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('@app/fitness/views/progression-chain/create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        $progressionChainExercises = $model->exercises;
        while (count($progressionChainExercises) <= 10) {
            $new = new ProgressionChainExercise;
            $new->progression_chain_id = $model->id;
            $progressionChainExercises[] = $new;
        }


        if ($model->load($post) && $model->save()) {

            foreach ($progressionChainExercises as $index => $progressionChainExercise) {
                $postItem = $post['ProgressionChainExercise'][$index];
                if(isset($postItem['exercise_id'])) {
                    $progressionChainExercise->exercise_id = $postItem['exercise_id'];
                }
                if(isset($postItem['difficulty_increase_percent'])) {
                    $progressionChainExercise->difficulty_increase_percent = $postItem['difficulty_increase_percent'];
                }

                if ($progressionChainExercise->exercise_id && ($index === 0 || $progressionChainExercise->difficulty_increase_percent)) {
                    $progressionChainExercise->id ? $progressionChainExercise->update() : $progressionChainExercise->save();
                }
            }
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/progression-chain/update', [
            'model' => $model,
            'progressionChainExercises' => $progressionChainExercises,
            'exerciseSelectOptions' => Exercise::getProgressionChainSelectOptions()
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionApiList()
    {
        $progressionChains = ProgressionChain::find()->asArray()->all();
        return json_encode($progressionChains);
    }

    protected function findModel($id)
    {
        if (($model = ProgressionChain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}