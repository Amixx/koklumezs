<?php

namespace app\models;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "lecturesdifficulties".
 *
 * @property int $id
 * @property int $diff_id Parametrs
 * @property int $lecture_id Lekcija
 * @property string $value Vērtība
 *
 * @property Lectures $lecture
 * @property Difficulties $diff
 */
class LecturesDifficulties extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lecturesdifficulties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diff_id', 'lecture_id', 'value'], 'required'],
            [['diff_id', 'lecture_id'], 'integer'],
            [['value'], 'string', 'max' => 50],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
            [['diff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Difficulties::className(), 'targetAttribute' => ['diff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'diff_id' => 'Parametrs',
            'lecture_id' => 'Lekcija',
            'value' => 'Vērtība',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiff()
    {
        return $this->hasOne(Difficulties::className(), ['id' => 'diff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLectureDifficulties($id)
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'diff_id', 'value');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function removeLectureDifficulties($id)
    {
        return self::deleteAll(['lecture_id' => $id]);
        //return self::find()->where(['lecture_id' => $id])->all()->delete();
    }
}
