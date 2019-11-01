<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "lecturesevaluations".
 *
 * @property int $id
 * @property int $lecture_id Lekcija
 * @property int $evaluation_id Novērtējums
 *
 * @property Lectures $lecture
 * @property Evaluations $evaluation
 */
class Lecturesevaluations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lecturesevaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'evaluation_id'], 'required'],
            [['lecture_id', 'evaluation_id'], 'integer'],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
            [['evaluation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evaluations::className(), 'targetAttribute' => ['evaluation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => 'Lekcija',
            'evaluation_id' => 'Novērtējums',
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
    public function getEvaluation()
    {
        return $this->hasOne(Evaluations::className(), ['id' => 'evaluation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLectureEvaluations($id)
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'evaluation_id', 'id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function removeLectureEvalutions($id)
    {
        return self::deleteAll(['lecture_id' => $id]);        
    }
}
