<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

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
            [['public_comment'], 'boolean'],
            [['created'], 'safe'],
            [['evaluation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evaluations::class, 'targetAttribute' => ['evaluation_id' => 'id']],
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
            'lecture_id' => \Yii::t('app',  'Lesson'),
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
        return $this->hasOne(Evaluations::class, ['id' => 'evaluation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluation()
    {
        return $this->hasOne(Evaluations::class, ['id' => 'evaluation_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLectureEvaluations($user_id, $id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $user_id, 'lecture_id' => $id])->orderBy(['id' => SORT_ASC])->asArray()->all(), 'evaluation_id', 'evaluation');
    }

    public function getCommentresponses()
    {
        return $this->hasMany(CommentResponses::class, ['id' => 'evaluation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function hasLectureEvaluations($user_id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_ASC])->asArray()->all(), 'lecture_id', 'evaluation_id');
    }

    public static function getCommentsResponsesForUser()
    {
        return self::getCommentsForUser();
    }

    public static function getCommentsForUser()
    {
        // $timeToStartShowingComments = new \DateTime('2020-06-27');
        $timeToStartShowingComments = new \DateTime('2019-06-27'); //for debugging
        $timeFormatted = $timeToStartShowingComments->format('Y-m-d');
        // $subquery = new Query()->select(['commentresponses.userlectureevaluation_id'])->from('commentresponses')->where();
        return self::find()->where(['user_id' => Yii::$app->user->identity->id, 'evaluation_id' => 4])->andWhere(['>=', 'created', $timeFormatted])->with('commentresponses')->asArray()->all();
    }

    public static function getComments($id)
    {
        $timeToStartShowingComments = new \DateTime('2020-06-27');
        // $timeToStartShowingComments = new \DateTime('2019-06-27'); for debugging
        $timeFormatted = $timeToStartShowingComments->format('Y-m-d');
        return self::find()->where(['evaluation_id' => 4, 'lecture_id' => $id])->andWhere(['>=', 'created', $timeFormatted])->orderBy(['created' => SORT_DESC])->joinWith('student')->asArray()->all();
    }

    public static function getLecturedifficultyEvaluation($userId, $lectureId)
    {
        $difficultyEvaluationId = 1;

        return self::find()->where([
            'lecture_id' => $lectureId,
            'evaluation_id' => $difficultyEvaluationId,
            'user_id' => $userId,
        ])->one();
    }
}
