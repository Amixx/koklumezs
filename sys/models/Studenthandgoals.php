<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "studenthandgoals".
 *
 * @property int $id
 * @property int $user_id Students
 * @property int $category_id Kategory
 *
 * @property Handdifficulties $category
 * @property Users $user
 */
class Studenthandgoals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studenthandgoals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id'], 'required'],
            [['user_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handdifficulties::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'user_id' => 'Students',
            'category_id' => 'Kategory',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Handdifficulties::className(), ['id' => 'category_id']);
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
    public function removeUserGoals($id)
    {
        return self::deleteAll(['user_id' => $id]);        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGoals($id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id])->asArray()->all(), 'category_id', 'id');      
    }

}