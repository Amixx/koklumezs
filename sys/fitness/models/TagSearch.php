<?php

namespace app\fitness\models;

use app\fitness\models\Tag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TagSearch extends Tag
{
    public $author;
    public function rules()
    {
        return [
            [['id', 'author_id'], 'integer'],
            [[
                'author_id',
                'value',
                'description',
                'type',
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
        $query = Tag::find();

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
        $query->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
