<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "difficulties".
 *
 * @property int $id
 * @property string $name Parametrs
 */
class SchoolFaqs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolfaqs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'question', 'answer'], 'required'],
            [['question', 'answer'], 'string'],
            [['school_id'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'question' => \Yii::t('app',  'Question'),
            'answer' => \Yii::t('app',  'Answer'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->asArray()->all();
    }

    public function getForCurrentSchool(){
        $schoolId = SchoolTeacher::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }
}
