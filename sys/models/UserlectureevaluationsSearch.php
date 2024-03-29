<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Userlectureevaluations;
use Yii;

/**
 * UserlectureevaluationsSearch represents the model behind the search form of `app\models\Userlectureevaluations`.
 */
class UserlectureevaluationsSearch extends Userlectureevaluations
{
    public $student;
    public $lecture;
    public $evalua;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lecture_id', 'evaluation_id', 'user_id'], 'integer'],
            [['evaluation', 'created'], 'safe'],
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
    public function search($params, $onlyComments = false)
    {
        $query = Userlectureevaluations::find();

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

        $query->joinWith(['evalua']);

        $dataProvider->sort->attributes['evalua'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['evalua.title' => SORT_ASC],
            'desc' => ['evalua.title' => SORT_DESC],
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

        $userContext = Yii::$app->user->identity;
        $defaultEvaluationId = $userContext->isTeacher() ? null : 4;

        $query->andFilterWhere([
            'id' => $this->id,
            'lecture_id' => $this->lecture_id,
            'evaluation_id' => $this->evaluation_id ? $this->evaluation_id : $defaultEvaluationId,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'evalua', $this->evalua]);

        $dataProvider->sort->defaultOrder = ['created' => SORT_DESC];

        return $dataProvider;
    }
}
