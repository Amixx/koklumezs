<?php

namespace app\models;

use app\models\Lectures;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\EmailSender;

class UserLectures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userlectures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'user_id', 'assigned'], 'required'],
            [['lecture_id', 'user_id', 'assigned', 'opened', 'sent'], 'integer'],
            [['created', 'opentime', 'sent', 'open_times', 'user_difficulty'], 'safe'],
            [['assigned'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['assigned' => 'id']],
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
            'lecture_id' => \Yii::t('app',  'Lesson'),
            'user_id' => \Yii::t('app',  'Student'),
            'assigned' => 'Administrators',
            'created' => \Yii::t('app',  'Created'),
            'opened' => \Yii::t('app',  'Opened'),
            'opentime' => \Yii::t('app',  'Opening time'),
            'sent' => \Yii::t('app',  'E-mail sent'),
            'evaluated' => \Yii::t('app',  'Evaluated'),
            'user_difficulty' => \Yii::t('app',  'Abilities'),
        ];
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
    public function getAdmin()
    {
        return $this->hasOne(Users::className(), ['id' => 'assigned'])
            ->from(['admin' => Users::tableName()]);
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
    public function getUserLectures($id, $sent = 1): array
    {
        //not anymore , 'sent' => $sent
        $results = self::find()->where(['user_id' => $id])->asArray()->all(); //, 'evaluated' => 0
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSentUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 0, 'sent' => $sent])->asArray()->orderBy(['created' => SORT_DESC])->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluatedUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1, 'sent' => $sent])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluatedLectures($id): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEvaluatedLecture($id)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTenEvaluatedLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTenLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllLectures($id)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDayResult($id, $days = 7)
    {
        $result = 0;
        $data = self::find()->where(['user_id' => $id])->andWhere('created >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)')->orderBy(['id' => SORT_DESC])->all();
        foreach ($data as $d) {
            $result += (int) $d->open_times;
        }
        return $result;
    }




    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEvaluatedLectures($ids)
    {
        return self::find()->where(['in', 'user_id', $ids])->andWhere(['evaluated' => 1])->orderBy(['id' => SORT_DESC])->asArray()->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLectureTimes($id): array
    {
        $results = self::find()->where(['user_id' => $id])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'created') : [];
    }

    public function getLastLecturesForUser($id, $limit = 5): array
    {
        $results = self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public function getLectures($id, $order, $evaluated = 0)
    {
        if ($order === 'asc') {
            $orderBy = ['lectures.complexity' => SORT_ASC];
        } else {
            $orderBy = ['lectures.complexity' => SORT_DESC];
        }
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated])->joinWith('lecture')->orderBy($orderBy)->all();
    }

    public function getLecturesOfType($id, $type)
    {
        $condition = ['user_id' => $id, 'sent' => true];
        if ($type == "new") {
            $condition['opened'] = false;
        } else if ($type == "learning") {
            $condition['still_learning'] = true;
        } else if ($type == "favourite") {
            $condition['is_favourite'] = true;
        }

        $results = self::find()->where($condition)->orderBy(['id' => SORT_DESC])->all();
        
        $results = $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
        
        return static::filterOutRelatedLessons($results);
    }

    public function getLatestLecturesOfType($id, $type)
    {
        return array_slice(self::getLecturesOfType($id, $type), 0, 8);
    }

    // remove all lessons, which appear as related lessons in a lesson, which has been assigned more recently
    // only the most recently assigned lesson remains
    public static function filterOutRelatedLessons($mappedIds){
        $result = $mappedIds;

        foreach($mappedIds as $userLectureId => $lectureId){
            $relatedLessonIds = RelatedLectures::getRelations($lectureId);

            if(!empty($relatedLessonIds)){
                $largestRelatedLessonId = max(array_keys($relatedLessonIds));
                $mostRecentUserLessonId = $largestRelatedLessonId > $userLectureId
                    ? $largestRelatedLessonId
                    : $lectureId;
               
                foreach($relatedLessonIds as $relatedLessonId => $lessonId){
                    $userLessonId = array_search((int)$lessonId, $result);
                    if($relatedLessonId != $mostRecentUserLessonId && $userLessonId !== false){
                        unset($result[$userLessonId]);
                    }
                }
            }
        }

        return $result;
    }

    public function getUnsentLectures($id, $evaluated = 0, $sent = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated, 'sent' => $sent])->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function setSeenByUser($user_id, $id)
    {
        $setOpenTime = false;
        if (($user = Users::findOne($user_id)) !== null) {
            $model = self::find()->where(['opened' => 0, 'user_id' => $user_id, 'lecture_id' => $id])->one();
            if ($model) {
                $model->opened = 1;
                $model->still_learning = 1;
                if (!$setOpenTime) {
                    $model->open_times = $model->open_times + 1;
                }
                $model->opentime = date('Y-m-d H:i:s', time());
                $setOpenTime = $model->update();
            } else {
                $model = self::find()->where(['opened' => 1, 'user_id' => $user_id, 'lecture_id' => $id])->one();
                if ($model and !$setOpenTime) {
                    $model->open_times = (int) $model->open_times + 1;
                    $setOpenTime = $model->update();
                }
            }
            $user->last_lecture = $id;
            $user->update();
        }
        return true;
    }

    public function getOpened($id)
    {
        $results = self::find()->where(['user_id' => $id, 'opened' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'id') : [];
    }

    public function getNewLectures($user_id, $ids = []): array
    {
        $ids = array_map('intval', $ids);
        $results = [];
        $r = self::find()->where(['user_id' => $user_id])->orderBy(['lecture_id' => SORT_ASC])->all();
        $current = $r ? ArrayHelper::map($r, 'id', 'lecture_id') : $results;
        if ($current) {
            $results = array_diff($ids, $current);
        } else {
            $results = $ids;
        }
        return $results;
    }

    public function getUserLectureByLectureId($lecture_id)
    {
        $user_id = Yii::$app->user->identity->id;

        return self::findOne(['user_id' => $user_id, 'lecture_id' => $lecture_id]);
    }

    public function getFirstOpentime($user_id)
    {
        $userLectures = self::find()->where(['user_id' => $user_id])->asArray()->all();
        $opentimes = array_map(function ($ulecture) {
            return $ulecture['opentime'];
        }, $userLectures);

        $firstOpenTime = null;
        foreach ($opentimes as $time) {
            if ($firstOpenTime == null || ($time !== null && $time < $firstOpenTime)) {
                $firstOpenTime = $time;
            }
        }
        return $firstOpenTime;
    }


    public static function getNextLessonId($studentId, $currentLectureId, $type){
        $lectureIds = self::getLecturesOfType($studentId, $type);
        $lectures = Lectures::find()->where(['in', 'id', $lectureIds])->orderBy(['title' => SORT_ASC])->asArray()->all();

        $takeNext = false;
        foreach($lectures as $lecture){
            if($takeNext) return $lecture['id'];
            if($lecture["id"] === $currentLectureId) $takeNext = true;
        }
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($id, $teacherMessage = null, $subject)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }

        $school = School::getByStudent($id);

        return EmailSender::sendLessonNotification($user, $teacherMessage, $school['email'], $subject);
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendAdminEmail($id, $lecture_id, $x)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }

        $school = School::getByStudent($id);
        $lecture = Lectures::findOne($lecture_id);
        
        return EmailSender::sendReminderToTeacher($user, $lecture, $x, $school['email']);
    }
}
