<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserLectures;

/**
 * UserLecturesSearch represents the model behind the search form of `app\models\UserLectures`.
 */
class UserLecturesSearch extends UserLectures
{
    public $admin;
    public $student;
    public $lecture;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lecture_id', 'user_id', 'assigned','opened'], 'integer'],
            [['createdtime','admin','student','lecture','created','opentime','opened'], 'safe'],
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
        $query = UserLectures::find();
       
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        //relations
        $query->joinWith(['admin']);
                
        $dataProvider->sort->attributes['admin'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['admin.email' => SORT_ASC],
            'desc' => ['admin.email' => SORT_DESC],
        ];

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
            ['like', 'admin.email', $this->assigned]
        );
        $query->andFilterWhere(
            ['like', 'student.email', $this->user_id]
        );
        $query->andFilterWhere(
            ['like', 'lecture.title', $this->lecture_id]
        );
        $query->andFilterWhere(
            ['like', self::tableName() . '.created', $this->created]
        );
        $query->andFilterWhere(
            ['like', 'opentime', $this->opentime]
        );
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lecture_id' => $this->lecture_id,
            'user_id' => $this->user_id,
            'assigned' => $this->assigned,
            'opened' => $this->opened,
        ]);
        

        return $dataProvider;
    }
}
