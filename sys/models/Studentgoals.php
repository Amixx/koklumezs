<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "studentgoals".
 *
 * @property int $id
 * @property int $user_id Lietotājs
 * @property string $type Veids
 * @property int $diff_id Parametrs
 * @property int $value Vērtība
 *
 * @property Difficulties $diff
 * @property Users $user
 */
class Studentgoals extends \yii\db\ActiveRecord
{
    const NOW = 'Šobrīd';
    const FUTURE = 'Vēlamais';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studentgoals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'diff_id', 'value'], 'required'],
            [['user_id', 'diff_id', 'value'], 'integer'],
            [['type'], 'string'],
            [['diff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Difficulties::className(), 'targetAttribute' => ['diff_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'type' => 'Veids',
            'diff_id' => 'Parametrs',
            'value' => 'Vērtība',
        ];
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
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGoals($id)
    {
        $data = self::findAll(['user_id' => $id]);
        $result = [];
        foreach($data as $d)
        {
            $result[$d->type][$d->diff_id] = $d->value;
        }
        return $result;        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function removeUserGoals($id)
    {
        return self::deleteAll(['user_id' => $id]);        
    }
}
