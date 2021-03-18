<?php

namespace app\models;

class CorrespondenceOpentimes extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'correspondenceopentimes';
    }

    public function rules()
    {
        return [
            [['author_id', 'recipient_id'], 'required'],
            [['author_id', 'recipient_id'], 'number'],
            [['opentime'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Parameter'),
        ];
    }

    public static function getOpentimeValue($authorId, $recipientId){
        $model = self::getOpentimeModel($authorId, $recipientId);
        return $model && isset($model['opentime']) ? $model['opentime'] : null;
    }

    public static function getOpentimeModel($authorId, $recipientId){
        return self::find()->where(['author_id' => $authorId, 'recipient_id' => $recipientId])->one();
    }

    public static function updateOpentime($authorId, $recipientId){
        $model = self::getOpentimeModel($authorId, $recipientId);

        if($model){
            $model->opentime = date('Y-m-d H:i:s', time());
            return $model->update();
        }else{
            $model = new CorrespondenceOpentimes;               
            $model->author_id = $authorId;
            $model->recipient_id = $recipientId;
            return $model->save();
        }
        
    }   
}
