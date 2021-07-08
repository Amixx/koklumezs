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
class NeedHelpMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'need_help_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'lesson_id', 'message'], 'required'],
            [['id', 'author_id', 'lesson_id'], 'integer'],
            [['message'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'message' => \Yii::t('app',  'Message text'),
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lesson_id']);
    }

    public static function getFormattedForChat($userId)
    {
        $formattedForChat = [];
        $messages = self::find()->where(['author_id' => $userId])->joinWith('author')->joinWith('lesson')->orderBy('id desc')->all();

        foreach ($messages as $message) {
            $formattedForChat[] = (object)[
                'message' => $message->message,
                'author' => $message->author,
                'lesson' => $message->lesson,
                'update_date' => $message->created_at,
                'is_need_help_message' => true,
            ];
        }

        return $formattedForChat;
    }
}
