<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UserSearch represents the model behind the search form about `app\models\Users`.
 */
class TeacherUserSearch extends Users
{
    public $lecture;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['first_name', 'last_name', 'username', 'language', 'subscription_type', 'status', 'last_login', 'dont_bother', 'user_level',], 'safe'],
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
        $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($currentUserTeacher->school_id);

        $query = Users::find()->where(['in', 'users.id', $schoolStudentIds])->joinWith("subplan");

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
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'user_level', $this->user_level])
            ->andFilterWhere(['like', 'subscription_type', $this->subscription_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'last_login', $this->last_login])
            ->andFilterWhere(['like', 'dont_bother', $this->dont_bother]);

        if(isset($params["TeacherUserSearch"]) && isset($params["TeacherUserSearch"]["subplan_monthly_cost"])) {
            $query->andFilterWhere(['like', 'schoolsubplans.id', $params["TeacherUserSearch"]["subplan_monthly_cost"]]);
        }
        if(isset($params["TeacherUserSearch"]) && isset($params["TeacherUserSearch"]["subplan_end_date"]) && $params["TeacherUserSearch"]["subplan_end_date"]) {
            $firstDayOfMonth = date_format((new \DateTime($params["TeacherUserSearch"]["subplan_end_date"]))
                ->modify('first day of this month'), 'Y-m-d');
            $lastDayOfMonth = date_format((new \DateTime($params["TeacherUserSearch"]["subplan_end_date"]))
                ->modify('last day of this month'), 'Y-m-d');
                
            $dateFilterString = '
                DATE_ADD(
                    DATE_ADD(studentsubplans.start_date, INTERVAL schoolsubplans.months MONTH),
                    INTERVAL (
                        SELECT COALESCE(sum(weeks), 0) FROM studentsubplanpauses
                        WHERE studentsubplan_id = studentsubplans.id) WEEK)
            ';
            if($firstDayOfMonth && $lastDayOfMonth){
                $query->andFilterWhere(['between', $dateFilterString, $firstDayOfMonth, $lastDayOfMonth]);
            }
        }
        if(isset($params["TeacherUserSearch"]) && isset($params["TeacherUserSearch"]["subplan_paid_type"]) && $params["TeacherUserSearch"]["subplan_paid_type"]) {
            $type = $params["TeacherUserSearch"]["subplan_paid_type"];
            if($type == "late"){
                $sign = '<';
            }else if($type == "paid"){
                $sign = '=';
            }else if($type == "prepaid"){
                $sign = '>';
            }

            $query->andWhere('studentsubplans.times_paid ' . $sign . ' studentsubplans.sent_invoices_count');  
        }        

        return $dataProvider;
    }
}