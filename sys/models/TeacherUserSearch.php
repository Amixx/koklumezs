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
            [['first_name', 'last_name', 'language', 'subscription_type', 'status', 'last_login', 'user_level',], 'safe'],
        ];
    }

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

        $query = Users::find()->where(['in', 'users.id', $schoolStudentIds])->andWhere(['is_deleted' => false]);

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
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'user_level', $this->user_level])
            ->andFilterWhere(['like', 'subscription_type', $this->subscription_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'last_login', $this->last_login]);

        if (isset($params["TeacherUserSearch"])) {
            $continue = $params["TeacherUserSearch"]["subplan_monthly_cost"]
                || $params["TeacherUserSearch"]["subplan_end_date"];

            if ($continue) {
                $studentSubplans = StudentSubPlans::find()->where(['in', 'user_id', $schoolStudentIds])->andWhere(['is_active' => true])->joinWith('plan');

                if ($params["TeacherUserSearch"]["subplan_monthly_cost"] !== "") {
                    $planPrices = SchoolSubPlans::getPrices();
                    $selectedPrice = $planPrices[$params["TeacherUserSearch"]["subplan_monthly_cost"]];

                    $filterSql = "SELECT id FROM schoolsubplans 
                        WHERE schoolsubplans.id IN (
                            SELECT schoolsubplan_id FROM schoolsubplanparts WHERE (
                                SELECT ROUND(SUM(monthly_cost), 2)
                                FROM planparts WHERE id IN (
                                    SELECT planpart_id FROM schoolsubplanparts WHERE schoolsubplan_id = schoolsubplans.id
                                )
                            ) = $selectedPrice
                        )";
                    $planIds = Yii::$app->db->createCommand($filterSql)->queryAll()[0];

                    $studentSubplans->andFilterWhere(['in', 'plan_id', $planIds]);
                }

                if ($params["TeacherUserSearch"]["subplan_end_date"]) {
                    $date = new \DateTime($params["TeacherUserSearch"]["subplan_end_date"]);
                    $firstDayOfMonth = date_format(
                        ($date)->modify('first day of this month'),
                        'Y-m-d'
                    );
                    $lastDayOfMonth = date_format(
                        ($date)->modify('last day of this month'),
                        'Y-m-d'
                    );

                    $filterSql = '
                        DATE_ADD(
                            DATE_ADD(studentsubplans.start_date, INTERVAL schoolsubplans.months MONTH),
                            INTERVAL (
                                SELECT COALESCE(sum(weeks), 0) FROM studentsubplanpauses
                                WHERE studentsubplan_id = studentsubplans.id) WEEK)
                    ';

                    if ($firstDayOfMonth && $lastDayOfMonth) {
                        $studentSubplans->andFilterWhere(['between', $filterSql, $firstDayOfMonth, $lastDayOfMonth]);
                    }
                }

                // if ($params["TeacherUserSearch"]["subplan_paid_type"]) {
                //     $type = $params["TeacherUserSearch"]["subplan_paid_type"];
                //     if ($type == "late") {
                //         $sign = '<';
                //     } else if ($type == "paid") {
                //         $sign = '=';
                //     } else if ($type == "prepaid") {
                //         $sign = '>';
                //     }

                //     $studentSubplans->andFilterWhere([$sign, 'times_paid', 'sent_invoices_count']);
                // }

                $studentSubplanData = $studentSubplans->asArray()->all();

                $userIds = [];
                foreach ($studentSubplanData as $studentSubplan) {
                    $userIds[] = $studentSubplan['user_id'];
                }

                $query->andFilterWhere(['in', 'users.id', $userIds]);
            }
        }

        return $dataProvider;
    }
}
