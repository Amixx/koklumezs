<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class CommentResponses extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'commentresponses';
    }

    public function rules()
    {
        return [
            [['author_id', 'userlectureevaluation_id', 'text'], 'required'],
            [['text'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'userlectureevaluation_id' => \Yii::t('app',  'Evaluation ID'),
            'text' => \Yii::t('app',  'Reply text'),
            'created' => \Yii::t('app',  'Date created'),
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }
    public function getUserlectureevaluation()
    {
        return $this->hasOne(Userlectureevaluations::class, ['id' => 'userlectureevaluation_id']);
    }


    public static function getAllCommentResponses()
    {
        $query =  self::find()->joinWith('author')->joinWith('userlectureevaluation');
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
        ]);
    }

    public static function getUnseenCommentsCount()
    {
        $user_id = Yii::$app->user->identity->id;
        $userLectureEvaluationIds = Userlectureevaluations::find()
            ->where(['evaluation_id' => 4, 'user_id' => $user_id])
            ->select('id');
        $commentResponses = static::find()
            ->where(['= any', 'userlectureevaluation_id', $userLectureEvaluationIds])
            ->andWhere(['seen_by_author' => false])
            ->joinWith('userlectureevaluation')
            ->joinWith('userlectureevaluation.lecture');
        return count($commentResponses->asArray()->all());
    }

    public static function getCommentResponsesForUser()
    {
        $user_id = Yii::$app->user->identity->id;
        $userLectureEvaluationIds = Userlectureevaluations::find()
            ->where(['evaluation_id' => 4, 'user_id' => $user_id])
            ->select('id');
        return static::find()
            ->where(['= any', 'userlectureevaluation_id', $userLectureEvaluationIds])
            ->joinWith('userlectureevaluation')
            ->joinWith('userlectureevaluation.lecture');
    }

    public static function markResponsesAsSeen()
    {
        $commentResponses = static::getCommentResponsesForUser()->asArray()->all();
        foreach ($commentResponses as $response) {
            $model = CommentResponses::findOne($response['id']);
            $model->seen_by_author = true;
        }
    }


    public static function getAllForUser()
    {
        return static::find()->where(['author_id' => Yii::$app->user->identity->id])->joinWith('author')->joinWith('userlectureevaluation')->joinWith('userlectureevaluation.lecture');
    }

    public static function getCommentResponses($id)
    {
        return static::find()->where(['userlectureevaluation_id' => $id])->joinWith('author')->asArray()->all();
    }
}
