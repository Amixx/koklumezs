<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class SchoolEvaluations extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolevaluations';
    }

    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['title', 'type'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'title' => \Yii::t('app',  'Title'),
            'type' => \Yii::t('app',  'Type'),
            'stars' => \Yii::t('app',  'Star count'),
            'star_text' => \Yii::t('app',  'Star texts'),
            'is_scale' => \Yii::t('app',  'Algorithm scale'),
            'is_video_param' => \Yii::t('app',  'Lesson frequency parameter'),
        ];
    }

    public function getLecturesevaluations()
    {
        return $this->hasMany(Lecturesevaluations::class, ['evaluation_id' => 'id']);
    }

    public function getUserlectureevaluations()
    {
        return $this->hasMany(Userlectureevaluations::class, ['evaluation_id' => 'id']);
    }

    public static function getEvaluations()
    {
        return self::find()->asArray()->all();
    }

    public static function getEvaluationsValueTexts()
    {
        $result = [];
        $data = self::getEvaluations();
        foreach ($data as $d) {
            if ($d['type'] == 'stars') {
                $arr = unserialize($d['star_text']);
                $new_array = [];
                foreach ($arr as $key => $value) {
                    $new_array[$key + 1] = $value;
                }
                $result[$d['id']] = $new_array;
            }
        }
        return $result;
    }

    public static function getEvaluationsTitles()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'title');
    }

    public static function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public static function getScaleParam()
    {
        return self::findOne([
            'is_scale' => 1,
        ]);
    }

    public static function getVideoParam()
    {
        return self::findOne([
            'is_video_param' => 1,
        ]);
    }
}
