<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "userlectureevaluations".
 *
 * @property int $id
 * @property int $lecture_id Lekcija
 * @property int $evaluation_id Novērtējums
 * @property int $user_id Students
 * @property string $evaluation Vērtējums
 * @property string $created Izveidots
 * 
 * @property Evaluations $evaluation0
 * @property Users $user
 * @property Lectures $lecture
 */
class Userlectureevaluations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userlectureevaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'evaluation_id', 'user_id', 'evaluation'], 'required'],
            [['lecture_id', 'evaluation_id', 'user_id'], 'integer'],
            [['evaluation'], 'string'],
            [['created'], 'safe'],
            [['evaluation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evaluations::className(), 'targetAttribute' => ['evaluation_id' => 'id']],
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
            'lecture_id' => \Yii::t('app',  'Lecture'),
            'evaluation_id' => \Yii::t('app',  'Evaluation'),
            'user_id' => \Yii::t('app',  'Student'),
            'evaluation' => \Yii::t('app',  'Evaluation'),
            'created' => \Yii::t('app',  'Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvalua()
    {
        return $this->hasOne(Evaluations::className(), ['id' => 'evaluation_id']);
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
    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluation()
    {
        return $this->hasOne(Evaluations::className(), ['id' => 'evaluation_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLectureEvaluations($user_id, $id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $user_id, 'lecture_id' => $id])->orderBy(['id' => SORT_ASC])->asArray()->all(), 'evaluation_id', 'evaluation');
    }

    public function getCommentresponses()
    {
        return $this->hasMany(CommentResponses::className(), ['id' => 'evaluation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function hasLectureEvaluations($user_id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_ASC])->asArray()->all(), 'lecture_id', 'evaluation_id');
    }

    public function getCommentsResponsesForUser()
    {
        $myComments = self::getCommentsForUser();
        return $myComments;
    }

    public function getCommentsForUser()
    {
        // $timeToStartShowingComments = new \DateTime('2020-06-27');
        $timeToStartShowingComments = new \DateTime('2019-06-27'); //for debugging
        $timeFormatted = $timeToStartShowingComments->format('Y-m-d');
        // $subquery = new Query()->select(['commentresponses.userlectureevaluation_id'])->from('commentresponses')->where();
        return self::find()->where(['user_id' => Yii::$app->user->identity->id, 'evaluation_id' => 4])->andWhere(['>=', 'created', $timeFormatted])->andWhere(['id', $subquery])->with('commentresponses')->asArray()->all();
    }

    public function getComments($id)
    {
        $timeToStartShowingComments = new \DateTime('2020-06-27');
        // $timeToStartShowingComments = new \DateTime('2019-06-27'); for debugging
        $timeFormatted = $timeToStartShowingComments->format('Y-m-d');
        return self::find()->where(['evaluation_id' => 4, 'lecture_id' => $id])->andWhere(['>=', 'created', $timeFormatted])->joinWith('student')->asArray()->all();
    }
}
