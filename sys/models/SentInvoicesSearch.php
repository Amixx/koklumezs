<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Handdifficulties;

class SentInvoicesSearch extends SentInvoices
{
    public function rules()
    {
        return [
            [['invoice_number', 'plan_name', 'plan_price', 'plan_start_date', 'sent_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = SentInvoices::getRealForCurrentSchool();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number])
            ->andFilterWhere(['like', 'plan_name', $this->plan_name])
            ->andFilterWhere(['like', 'plan_price', $this->plan_price])
            ->andFilterWhere(['like', 'plan_start_date', $this->plan_start_date])
            ->andFilterWhere(['like', 'sent_date', $this->sent_date]);

        return $dataProvider;
    }
}
