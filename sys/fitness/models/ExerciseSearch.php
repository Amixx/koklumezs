<?php

namespace app\fitness\models;

use app\fitness\models\Exercise;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExerciseSearch extends Exercise
{
    public $exerciseTag;
    public function rules()
    {
        return [
            [['id', 'author_id', 'exerciseTag'], 'integer'],
            [[
                'author_id',
                'name',
                'description',
                'is_pause',
                'needs_evaluation',
                'popularity_type',
                'is_archived',
                'video',
                'technique_video',
                'created_at',
                'updated_at',
                'exerciseTag',
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
        $query = Exercise::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if($this->exerciseTag) {
            $query->andFilterWhere(['in', 'id', ExerciseTag::find()->select('exercise_id')->where(['tag_id' => $this->exerciseTag])]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'technique_video', $this->technique_video])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        if($this->is_pause !== null) {
            $query->andFilterWhere(['is_pause' => $this->is_pause]);
        }
        if($this->needs_evaluation !== null) {
            $query->andFilterWhere(['needs_evaluation' => $this->needs_evaluation]);
        }
        if($this->popularity_type !== null) {
            $query->andFilterWhere(['popularity_type' => $this->popularity_type]);
        }
        if($this->is_archived !== null) {
            $query->andFilterWhere(['is_archived' => $this->is_archived]);
        }

        return $dataProvider;
    }
}
