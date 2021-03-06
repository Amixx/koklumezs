<?php

namespace app\models;

use app\models\Lectures;
use app\models\Users;

/**
 * This is the model class for table "lectureViews".
 *
 * @property int $id
 * @property int $lecture_id Lekcija
 * @property int $user_id Students
 * @property int $assigned Administrators
 * @property string $createdtime Izveidots
 *
 * @property Users $assigned
 * @property Lectures $lecture
 */
class LectureViews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lectureviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'user_id'], 'required'],
            [['lecture_id', 'user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student'),
            'lecture_id' => \Yii::t('app',  'Lesson'),
            'datetime' => \Yii::t('app',  'Date'),
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    public static function getDayResult($id, $days = 7)
    {
        $data = self::find()
            ->where(['user_id' => $id])
            ->andWhere('datetime >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)')
            ->orderBy(['id' => SORT_DESC])->all();
        return count($data);
    }
}
