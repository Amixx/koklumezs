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
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
            [['evaluation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evaluations::class, 'targetAttribute' => ['evaluation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => \Yii::t('app',  'Lesson'),
            'evaluation_id' => \Yii::t('app',  'Evaluation'),
        ];
    }

    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluation()
    {
        return $this->hasOne(Evaluations::class, ['id' => 'evaluation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLectureEvaluations($id)
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'evaluation_id', 'id');
    }

    public static function removeLectureEvalutions($id)
    {
        return self::deleteAll(['lecture_id' => $id]);
    }
}
