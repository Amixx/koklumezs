<?php

namespace app\fitness\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ExerciseSearch extends Exercise
{
    public $exerciseTag;
    public $isAddedToAnyWorkouts;
    public $isAddedToAnyProgressionChains = null;
    public $interchangeableExercisesCount = null;

    public function rules()
    {
        return [
            [['id', 'author_id', 'exerciseTag'], 'integer'],
            [['is_ready', 'isAddedToAnyWorkouts'], 'boolean'],
            [[
                'author_id',
                'name',
                'popularity_type',
                'video',
                'technique_video',
                'is_bodyweight',
                'is_ready',
                'created_at',
                'updated_at',
                'exerciseTag',
                'isAddedToAnyWorkouts',
                'isAddedToAnyProgressionChains',
                'interchangeableExercisesCount',
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Exercise::find()->where([
            'is_archived' => false,
            'fitness_exercises.author_id' => Yii::$app->user->identity->id,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->exerciseTag) {
            $query->andFilterWhere(['in', 'id', ExerciseTag::find()->select('exercise_id')->where(['tag_id' => $this->exerciseTag])]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'technique_video', $this->technique_video])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        if ($this->is_bodyweight !== null) {
            $condition = $this->is_bodyweight === 'not set'
                ? ['is', 'is_bodyweight', new Expression('NULL')]
                : ['is_bodyweight' => $this->is_bodyweight];
            $query->andFilterWhere($condition);
        }
        if ($this->is_ready !== null && $this->is_ready !== '') {
            $query->andFilterWhere(['is_ready' => $this->is_ready]);
        }
        if ($this->isAddedToAnyWorkouts !== null && $this->isAddedToAnyWorkouts !== '') {
            $usedExerciseExerciseIds = WorkoutExercise::find()->select('exercise_id')->asArray()->all();
            $query->andFilterWhere([
                $this->isAddedToAnyWorkouts ? 'in' : 'not in',
                'id',
                ArrayHelper::getColumn($usedExerciseExerciseIds, 'exercise_id'),
            ]);
        }
        if ($this->isAddedToAnyProgressionChains !== null && $this->isAddedToAnyProgressionChains !== '') {
            $exerciseIdsInProgressionChains = ProgressionChainExercise::find()->select('exercise_id')->asArray()->all();
            $query->andFilterWhere([
                $this->isAddedToAnyProgressionChains ? 'in' : 'not in',
                'id',
                ArrayHelper::getColumn($exerciseIdsInProgressionChains, 'exercise_id'),
            ]);
        }
        if ($this->interchangeableExercisesCount !== null && $this->interchangeableExercisesCount !== '') {
            $queryEnd = $this->interchangeableExercisesCount === "5+" ? ">= 5" : "= $this->interchangeableExercisesCount";
            $filterSql = "
                select fitness_exercises.id from fitness_exercises join fitness_interchangeable_exercises
                on fitness_interchangeable_exercises.exercise_id_1 = fitness_exercises.id
                   or fitness_interchangeable_exercises.exercise_id_2 = fitness_exercises.id
                group by fitness_exercises.id
                having COUNT(*) $queryEnd";
            $query->andWhere(['in', 'id', Yii::$app->db->createCommand($filterSql)->queryAll()]);
        }

        return $dataProvider;
    }
}
