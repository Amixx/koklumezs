<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sentlectures".
 *
 * @property int $id
 * @property int $user_id Lietotājs
 * @property int $lecture_id Pēdējā nodarbīa
 * @property int $sent Nosūtīts e-pasts
 * @property string $created Izveidots
 *
 * @property Users $user
 * @property Lectures $lecture
 */
class Sentlectures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sentlectures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'lecture_id'], 'required'],
            [['user_id', 'lecture_id', 'sent'], 'integer'],
            [['created'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'User'),
            'lecture_id' => \Yii::t('app',  'Last lesson'),
            'sent' => \Yii::t('app',  'E-mail sent'),
            'created' => \Yii::t('app',  'Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }

    public static function getLectureCount($user = null, $lecture = null)
    {
        return self::find(['user_id' => $user, 'lecture_id' => $lecture])->count();
    }
}
