<?php

namespace app\fitness\models;

use app\fitness\models\Exercise;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExerciseSearch extends Exercise
{
    public $author;
    public function rules()
    {
        return [
            [['id', 'author_id'], 'integer'],
            [[
                'author_id',
                'name',
                'first_set_video',
                'other_sets_video',
                'technique_video',
                'created_at',
                'updated_at',
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

        $query->joinWith(['author']);

        $dataProvider->sort->attributes['author'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['u2.email' => SORT_ASC],
            'desc' => ['u2.email' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'author_id' => $this->author_id,
        ]);
        $query->andFilterWhere(
            ['like', 'u2.email', $this->author]
        );
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'first_set_video', $this->first_set_video])
            ->andFilterWhere(['like', 'other_sets_video', $this->other_sets_video])
            ->andFilterWhere(['like', 'technique_video', $this->technique_video])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
