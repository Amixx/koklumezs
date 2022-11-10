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
                'popularity_type',
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
        $query = Exercise::find()->where(['is_archived' => false]);

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
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'technique_video', $this->technique_video])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        if($this->popularity_type !== null) {
            $query->andFilterWhere(['popularity_type' => $this->popularity_type]);
        }

        return $dataProvider;
    }
}
