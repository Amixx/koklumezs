<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sentlectures".
 *
 * @property int $id
 * @property int $user_id Lietotājs
 * @property int $lecture_id Pēdējā lekcija
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
            'user_id' => 'Lietotājs',
            'lecture_id' => 'Pēdējā lekcija',
            'sent' => 'Nosūtīts e-pasts',
            'created' => 'Izveidots',
        ];
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

    public function getLectureCount($user = null,$lecture = null)
    {
        return self::find(['user_id' => $user, 'lecture_id' => $lecture])->count();
    }
}
