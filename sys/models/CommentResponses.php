<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class CommentResponses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commentresponses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'userlectureevaluation_id', 'text'], 'required'],
            [['text'], 'string'],
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
            'userlectureevaluation_id' => \Yii::t('app',  'Evaluation ID'),
            'text' => \Yii::t('app',  'Reply text'),
            'created' => \Yii::t('app',  'Date created'),
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['id' => 'author_id']);
    }
    public function getUserlectureevaluation()
    {
        return $this->hasOne(Userlectureevaluations::className(), ['id' => 'userlectureevaluation_id']);
    }


    public function getAllCommentResponses()
    {
        $query =  self::find()->joinWith('author')->joinWith('userlectureevaluation');
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created' => SORT_DESC]],
        ]);
    }

    public function getUnseenCommentsCount()
    {
        $user_id = Yii::$app->user->identity->id;
        $userLectureEvaluationIds = Userlectureevaluations::find()->where(['evaluation_id' => 4, 'user_id' => $user_id])->select('id');
        $commentResponses = self::find()->where(['= any', 'userlectureevaluation_id', $userLectureEvaluationIds])->andWhere(['seen_by_author' => false])->joinWith('userlectureevaluation')->joinWith('userlectureevaluation.lecture');
        return count($commentResponses->asArray()->all());
    }

    public function getCommentResponsesForUser()
    {
        $user_id = Yii::$app->user->identity->id;
        $userLectureEvaluationIds = Userlectureevaluations::find()->where(['evaluation_id' => 4, 'user_id' => $user_id])->select('id');
        $commentResponses = self::find()->where(['= any', 'userlectureevaluation_id', $userLectureEvaluationIds])->joinWith('userlectureevaluation')->joinWith('userlectureevaluation.lecture');

        return $commentResponses;
    }

    public function markResponsesAsSeen()
    {
        $commentResponses = self::getCommentResponsesForUser()->asArray()->all();
        foreach ($commentResponses as $response) {
            $model = CommentResponses::findOne($response['id']);
            $model->seen_by_author = true;
        }
    }


    public function getAllForUser()
    {
        return self::find()->where(['author_id' => Yii::$app->user->identity->id])->joinWith('author')->joinWith('userlectureevaluation')->joinWith('userlectureevaluation.lecture');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentResponses($id)
    {
        return self::find()->where(['userlectureevaluation_id' => $id])->joinWith('author')->asArray()->all();
    }
}
