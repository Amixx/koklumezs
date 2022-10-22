<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\Exercise;
use app\models\Users;
use app\fitness\models\ExerciseSearch;
use app\fitness\models\ExerciseTag;
use app\fitness\models\Tag;
use Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExerciseController extends Controller
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
        $searchModel = new ExerciseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->setPageSize(100);
        $get = Yii::$app->request->queryParams;

        return $this->render('@app/fitness/views/exercise/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get,
        ]);
    }

    public function actionView($id)
    {
        $model = Exercise::find()->where(['fitness_exercises.id' => $id])->joinWith('sets')->one();
        return $this->render('@app/fitness/views/exercise/view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new Exercise();
        $model->author_id = Yii::$app->user->identity->id;

        $tags = Tag::find()->asArray()->all();

        if ($model->load($post) && $model->save()) {
            if (isset($post['tags'])) {
                foreach ($post['tags'] as $tagId) {
                    $exerciseTag = new ExerciseTag;
                    $exerciseTag->exercise_id = $model->id;
                    $exerciseTag->tag_id = $tagId;
                    $exerciseTag->save();
                }
            }
            return $this->redirect(['index']);
        }

        return $this->render('@app/fitness/views/exercise/create', [
            'model' => $model,
            'tags' => $tags,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);
        $selectedTagIds = ArrayHelper::getColumn($model['exerciseTags'], 'tag_id');
        $tags = Tag::find()->asArray()->all();

        if ($model->load($post) && $model->save()) {
            if (isset($post['tags'])) {
                $removedTagIds = array_diff($selectedTagIds, $post['tags']);
                $addedTagIds = array_diff($post['tags'], $selectedTagIds);

                foreach ($addedTagIds as $tagId) {
                    $exerciseTag = new ExerciseTag;
                    $exerciseTag->exercise_id = $model->id;
                    $exerciseTag->tag_id = $tagId;
                    $exerciseTag->save();
                }
                foreach ($removedTagIds as $tagId) {
                    ExerciseTag::find()->where(['tag_id' => $tagId])->one()->delete();
                }
            } else {
                foreach ($selectedTagIds as $tagId) {
                    ExerciseTag::find()->where(['tag_id' => $tagId])->one()->delete();
                }
            }

            return $this->redirect(Url::previous());
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/exercise/update', [
            'model' => $model,
            'tags' => $tags,
            'selectedTagIds' => $selectedTagIds,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionApiList()
    {
        $get = Yii::$app->request->get();

        $query = Exercise::find()
            ->joinWith('sets')
            ->joinWith('exerciseTags')
            ->groupBy('name')
            ->limit(10);

        if (isset($get['tagIdGroups']) && $get['tagIdGroups']) {
            $tagIdGroups = array_map(function ($tagIdGroup) {
                return json_decode($tagIdGroup);
            }, $get['tagIdGroups']);


            foreach ($tagIdGroups as $tagIdGroup) {
                if (!empty($tagIdGroup)) {
                    $count = count($tagIdGroup);

                    $query->orWhere(
                        [
                            'in',
                            'fitness_exercises.id',
                            ExerciseTag::find()
                                ->select('fitness_exercisetags.exercise_id')
                                ->where(['in', 'tag_id', $tagIdGroup])
                                ->groupBy('fitness_exercisetags.exercise_id')
                                ->having("COUNT(*) = $count")
                        ]);
                }
            }
        }

        if (isset($get['exerciseName']) && $get['exerciseName'] && $get['exerciseName'] != '') {
            $query->andFilterWhere(['like', 'fitness_exercises.name', $get['exerciseName']]);
        }
        if (isset($get['tagTypes']) && $get['tagTypes']) {
            $query->andFilterWhere([
                'in',
                'fitness_exercises.id',
                ExerciseTag::find()
                    ->joinWith('tag')
                    ->select('fitness_exercisetags.exercise_id')
                    ->where(['in', 'type', $get['tagTypes']])
            ]);
        }

        $query->andFilterWhere(['is_pause' => !!(isset($get['onlyPauses']) && $get['onlyPauses'])]);
        if(isset($get['exercisePopularity']) && $get['exercisePopularity']) {
            $query->andFilterWhere(['popularity_type' => $get['exercisePopularity']]);
        }

        $exercises = $query->asArray()->all();

        return json_encode($exercises);
    }

    protected function findModel($id)
    {
        if (($model = Exercise::find()->where(['fitness_exercises.id' => $id])->joinWith('exerciseTags')->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
