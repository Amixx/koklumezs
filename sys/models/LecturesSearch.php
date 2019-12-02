<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lectures;

/**
 * LecturesSearch represents the model behind the search form of `app\models\Lectures`.
 */
class LecturesSearch extends Lectures
{
    public $users;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'author'], 'integer'],
            [['title', 'description', 'created', 'updated', 'complexity', 'author','users','season'], 'safe'],
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
        $query = Lectures::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //relations
        $query->joinWith(['users']);
        
        $dataProvider->sort->attributes['users'] = [
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
            'author' => $this->author,
            'season' => $this->season,
            'complexity' => $this->complexity
        ]);
        $query->andFilterWhere(
            ['like', 'u2.email', $this->users]
        );
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])           
            ->andFilterWhere(['like', 'created', $this->created])
            ->andFilterWhere(['like', 'updated', $this->updated]);

        return $dataProvider;
    }
}
