<?php

namespace app\fitness\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ExerciseSearch extends Exercise
{
    public $exerciseTag;
    public $isAddedToAnyWorkouts;

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
                'is_ready',
                'created_at',
                'updated_at',
                'exerciseTag',
                'isAddedToAnyWorkouts'
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

        if ($this->popularity_type !== null && $this->popularity_type !== '') {
            $query->andFilterWhere(['popularity_type' => $this->popularity_type]);
        }
        if ($this->is_ready !== null && $this->is_ready !== '') {
            $query->andFilterWhere(['is_ready' => $this->is_ready]);
        }
        if ($this->isAddedToAnyWorkouts !== null && $this->isAddedToAnyWorkouts !== '') {
            $x = WorkoutExercise::find()->select('exercise_id')->asArray()->all();
            $query->andFilterWhere([
                $this->isAddedToAnyWorkouts ? 'in' : 'not in',
                'id',
                ArrayHelper::getColumn($x, 'exercise_id'),
            ]);
        }

        return $dataProvider;
    }
}
