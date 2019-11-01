<?php

namespace app\models;

use Yii;

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
}
