<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SentInvoicesSearch extends SentInvoices
{
    public function rules()
    {
        return [
            [['invoice_number', 'is_advance', 'plan_name', 'plan_price', 'plan_start_date', 'sent_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $userContext = Yii::$app->user->identity;
        
        $query = $userContext->isTeacher() ? SentInvoices::getForCurrentSchool() : SentInvoices::getForAllSchools();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number])
            ->andFilterWhere(['like', 'is_advance', $this->is_advance])
            ->andFilterWhere(['like', 'plan_name', $this->plan_name])
            ->andFilterWhere(['like', 'plan_price', $this->plan_price])
            ->andFilterWhere(['like', 'plan_start_date', $this->plan_start_date])
            ->andFilterWhere(['like', 'sent_date', $this->sent_date]);

        return $dataProvider;
    }
}
