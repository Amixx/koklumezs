<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UserSearch represents the model behind the search form about `app\models\Users`.
 */
class UserSearch extends Users
{
    public $lecture;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'last_lecture'], 'integer'],
            [[
                'first_name',
                'last_name',
                'phone_number',
                'email',
                'user_level',
                'language',
                'subscription_type',
                'status',
                'user_level',
                'last_login',
                'last_lecture',
                'allowed_to_download_files'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Users::find()->where(['is_deleted' => false])->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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

        $query->andFilterWhere([
            'id' => $this->id,
            'last_lecture' => $this->last_lecture
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user_level', $this->user_level])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'subscription_type', $this->subscription_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'last_login', $this->last_login])
            ->andFilterWhere(['like', 'allowed_to_download_files', $this->allowed_to_download_files]);

        return $dataProvider;
    }
}
