<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Userlectureevaluations;

class TeacherUserlectureevaluationsSearch extends Userlectureevaluations
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
    public function search($params, $onlyComments)
    {
        $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
        $schoolLectureIds = SchoolLecture::getSchoolLectureIds($currentUserTeacher->school_id);
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($currentUserTeacher->school_id);

        $query = Userlectureevaluations::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'lecture_id' => $this->lecture_id,
            'evaluation_id' => $this->evaluation_id ? $this->evaluation_id : null,
            'user_id' => $this->user_id,
        ]);

        if ($onlyComments) {
            $query->andFilterWhere(['evaluation_id' => 4]);
        }

        if (count($schoolLectureIds) > 0 and count($schoolStudentIds) > 0) {
            $query->andFilterWhere([
                'AND',
                ['in', 'lecture_id', $schoolLectureIds],
                ['in', 'user_id', $schoolStudentIds],
            ]);
        } else {
            $query->andFilterWhere(['user_id' => -1]);
        }



        $query->andFilterWhere(['like', 'evalua', $this->evalua]);

        $dataProvider->sort->defaultOrder = ['created' => SORT_DESC];

        return $dataProvider;
    }
}
