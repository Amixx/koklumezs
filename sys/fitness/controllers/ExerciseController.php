<?php

namespace app\fitness\controllers;

use app\fitness\models\InterchangeableExercise;
use Yii;
use app\fitness\models\Exercise;
use app\models\Users;
use app\fitness\models\ExerciseSearch;
use app\fitness\models\ExerciseTag;
use app\fitness\models\Tag;
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
        $model->needs_evaluation = true;
        $model->popularity_type = 'AVERAGE';

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
        $interchangeableExerciseIds = $model->getInterchangeableExerciseIds();
        $interchangeableExerciseSelectedOptions = $model->getInterchangeableExercisesSelect2Options();
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


            if (isset($post['interchangeableExercises'])) {
                $removedInterchangeableExerciseIds = array_diff($interchangeableExerciseIds, $post['interchangeableExercises']);
                $addedInterchangeableExerciseIds = array_diff($post['interchangeableExercises'], $interchangeableExerciseIds);

                foreach ($addedInterchangeableExerciseIds as $ieid) {
                    if($ieid == $model->id) continue;
                    $interchangeableExercise = new InterchangeableExercise;
                    $interchangeableExercise->exercise_id_1 = $model->id;
                    $interchangeableExercise->exercise_id_2 = $ieid;
                    $interchangeableExercise->save();
                }

                InterchangeableExercise::deleteAll(['or',
                    ['in', 'exercise_id_1', $removedInterchangeableExerciseIds],
                    ['in', 'exercise_id_2', $removedInterchangeableExerciseIds]
                ]);
            } else {
                InterchangeableExercise::deleteAll(['or',
                    ['in', 'exercise_id_1', $interchangeableExerciseIds],
                    ['in', 'exercise_id_2', $interchangeableExerciseIds]
                ]);
            }


            return $this->redirect(['fitness-exercises/view', 'id' => $id]);
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/exercise/update', [
            'model' => $model,
            'tags' => $tags,
            'selectedTagIds' => $selectedTagIds,
            'interchangeableExerciseSelectedOptions' => $interchangeableExerciseSelectedOptions,
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

        $limit = 20;

        $query = Exercise::find()
            ->andWhere([
                'fitness_exercises.author_id' => Yii::$app->user->identity->id
            ])
            ->joinWith('sets')
            ->groupBy('name')
            ->limit($limit);

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
        if(!isset($get['onlyPauses'])) {
            $query->andWhere(['is_archived' => 0]);
        }
        if (isset($get['exercisePopularity']) && $get['exercisePopularity']) {
            $query->andFilterWhere(['popularity_type' => $get['exercisePopularity']]);
        }

        $exercises = $query->joinWith('videos')->asArray()->all();

        return json_encode($exercises);
    }

    public function actionApiCreate()
    {
        $post = Yii::$app->request->post();
        $exercise = new Exercise;
        $exercise->author_id = Yii::$app->user->identity->id;
        $exercise->name = $post['name'];
        if (isset($post['description']) && $post['description']) {
            $exercise->description = $post['description'];
        }
        $exercise->popularity_type = 'AVERAGE';

        if ($exercise->save()) {
            return json_encode(ArrayHelper::toArray($exercise));
        }
        return null;
    }

    public function actionForSelect()
    {
        $get = Yii::$app->request->get();
        $exerciseNameSearchTerm = $get['term'];

        $exercises = Exercise::find()
            ->where(['author_id' => Yii::$app->user->identity->id])
            ->andWhere(['like', 'name', $exerciseNameSearchTerm])
            ->limit(50)->asArray()->all();
        $select2Options = array_map(function ($exercise) {
            return [
                'id' => $exercise['id'],
                'text' => $exercise['name'],
            ];
        }, $exercises);

        $response = [
            'results' => $select2Options
        ];

        return json_encode($response);
    }

    public function actionApiGetAverageAbility($id, $userId){
        $exercise = Exercise::findOne(['id' => $id]);

        $x = $exercise->estimatedAvgAbilityOfUser($userId);

        if(!$x) return $x;
        
        return json_encode([
            'ability' => round($x['ability'], 1),
            'type' => $x['type'],
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Exercise::find()->where(['fitness_exercises.id' => $id])->joinWith('exerciseTags')->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
