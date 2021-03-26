<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sentlectures;

/**
 * SentlecturesSearch represents the model behind the search form of `app\models\Sentlectures`.
 */
class SentlecturesSearch extends Sentlectures
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'lecture_id', 'sent'], 'integer'],
            [['created'], 'safe'],
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
        $query = Sentlectures::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //relations
        $query->joinWith(['student']);

        $dataProvider->sort->attributes['student'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['student.email' => SORT_ASC],
            'desc' => ['student.email' => SORT_DESC],
        ];

        //relations
        $query->joinWith(['lecture']);

        $dataProvider->sort->attributes['lecture'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['lecture.title' => SORT_ASC],
            'desc' => ['lecture.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(
            ['like', self::tableName() . '.created', $this->created]
        );
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'lecture_id' => $this->lecture_id,
            'sent' => $this->sent,
        ]);

        return $dataProvider;
    }
}
