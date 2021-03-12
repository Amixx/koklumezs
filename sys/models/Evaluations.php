<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class Evaluations extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'evaluations';
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
        return $this->hasMany(Lecturesevaluations::className(), ['evaluation_id' => 'id']);
    }

    public function getUserlectureevaluations()
    {
        return $this->hasMany(Userlectureevaluations::className(), ['evaluation_id' => 'id']);
    }

    public static function getEvaluations()
    {
        return self::find()->where(['not like', 'id', [2,3,5]])->asArray()->all();
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
        return ArrayHelper::map(self::find()->where(['not like', 'id', [2,3,5]])->asArray()->all(), 'id', 'title');
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
