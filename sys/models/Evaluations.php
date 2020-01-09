<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "evaluations".
 *
 * @property int $id
 * @property string $title Virsraksts
 * @property string $type Tips
 *
 * @property Lecturesevaluations[] $lecturesevaluations
 * @property Userlectureevaluations[] $userlectureevaluations
 */
class Evaluations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['title', 'type'], 'string'],           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Virsraksts',
            'type' => 'Tips',
            'stars' => 'Zvaigžņu skaits',
            'star_text' => 'Zvaigžņu teksti',
            'is_scale' => 'Algoritma skala',
            'is_video_param' => 'Lekciju biežuma parametrs',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecturesevaluations()
    {
        return $this->hasMany(Lecturesevaluations::className(), ['evaluation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserlectureevaluations()
    {
        return $this->hasMany(Userlectureevaluations::className(), ['evaluation_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvaluations()
    {
        return self::find()->asArray()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getEvaluationsValueTexts()
    {
        $result = [];
        $data = self::getEvaluations();
        foreach($data as $d){
            if($d['type'] == 'zvaigznes'){
                $arr = unserialize($d['star_text']);
                $new_array = [];
                foreach($arr as $key => $value){
                    $new_array[$key+1] = $value;
                }
                $result[$d['id']] = $new_array;
            }
        }
        return $result;
    }

    

    /**
     * {@inheritdoc}
     */
    public function getEvaluationsTitles()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'title');
    }

    public function getScaleParam()
    {
        return self::findOne([
            'is_scale' => 1,
        ]);
    }

    public function getVideoParam()
    {
        return self::findOne([
            'is_video_param' => 1,
        ]);
    }

}
