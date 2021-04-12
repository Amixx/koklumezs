<?php

namespace app\models;

use app\models\Lectures;
use app\models\Users;
use app\models\School;
use app\models\SchoolLecture;
use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\EmailSender;

class UserLectures extends \yii\db\ActiveRecord
{
    const STILL_LEARNING_TRANSITION_DATE = "2021-03-13 00:00:00";

    public static function tableName()
    {
        return 'userlectures';
    }

    public function rules()
    {
        return [
            [['lecture_id', 'user_id', 'assigned'], 'required'],
            [['lecture_id', 'user_id', 'assigned', 'opened', 'sent'], 'integer'],
            [['created', 'opentime', 'sent', 'open_times', 'user_difficulty'], 'safe'],
            [['assigned'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['assigned' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

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

    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    public function getAdmin()
    {
        return $this->hasOne(Users::class, ['id' => 'assigned'])
            ->from(['admin' => Users::tableName()]);
    }

    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    public static function getUserLectures($id, $sent = 1): array
    {
        //not anymore , 'sent' => $sent
        $results = self::find()->where(['user_id' => $id])->asArray()->all(); //, 'evaluated' => 0
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getSentUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 0, 'sent' => $sent])->asArray()->orderBy(['created' => SORT_DESC])->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getEvaluatedUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1, 'sent' => $sent])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    //izgūst tās nodarbības, kas līdz šim (3/12/2021) bijušas nenovērtētas un pie 'vēl mācos' - tām turpmāk jābūt arhīvā
    public static function getUnevaluatedStillLearning($id)
    {
        $results = self::find()
            ->where(['user_id' => $id, 'evaluated' => 0, 'still_learning' => 1])
            ->andWhere(['<', 'created', self::STILL_LEARNING_TRANSITION_DATE])
            ->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    //izgūst tās nodarbības, kas līdz šim (3/12/2021) bijušas novērtētas un pie 'vēl mācos' - tām turpmāk jābūt pie mīļākajām
    public static function getEvaluatedStillLearning($id)
    {
        $results = self::find()
            ->where(['user_id' => $id, 'evaluated' => 1, 'still_learning' => 1])
            ->andWhere(['<', 'created', self::STILL_LEARNING_TRANSITION_DATE])
            ->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getEvaluatedLectures($id): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getLastEvaluatedLecture($id)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->one();
    }

    public static function getLastTenEvaluatedLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    public static function getLastTenLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    public static function getAllLectures($id)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->all();
    }

    public static function getDayResult($id, $days = 7)
    {
        $result = 0;
        $data = self::find()->where(['user_id' => $id])->andWhere('created >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)')->orderBy(['id' => SORT_DESC])->all();
        foreach ($data as $d) {
            $result += (int) $d->open_times;
        }
        return $result;
    }

    public static function getLastEvaluatedLectures($ids)
    {
        return self::find()->where(['in', 'user_id', $ids])->andWhere(['evaluated' => 1])->orderBy(['id' => SORT_DESC])->asArray()->all();
    }

    public static function getUserLectureTimes($id): array
    {
        $results = self::find()->where(['user_id' => $id])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'created') : [];
    }

    public static function getLastLecturesForUser($id, $limit = 5): array
    {
        $results = self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getLectures($id, $order, $evaluated = 0)
    {
        if ($order === 'asc') {
            $orderBy = ['lectures.complexity' => SORT_ASC];
        } else {
            $orderBy = ['lectures.complexity' => SORT_DESC];
        }
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated])->joinWith('lecture')->orderBy($orderBy)->all();
    }

    public static function getLessonsOfType($id, $type)
    {
        $condition = ['user_id' => $id, 'sent' => true, 'still_learning' => false];

        if ($type == "new") {
            $condition['evaluated'] = false;
        } else if ($type == "favourite") {
            $condition['is_favourite'] = true;
        }

        $results = self::find()->where($condition)->orderBy(['id' => SORT_DESC])->all();

        // visas nodarbības, kas piešķirtas pirms update un bijušas atvērtas, tagad atrodas arhīvā
        if ($type === "new") {
            $results = self::filterOutOldOpenedLessons($results);
        }

        $results = $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
        return static::filterOutRelatedLessons($results);
    }

    public static function getLatestLessonsOfType($id, $type)
    {
        return array_slice(self::getLessonsOfType($id, $type), 0, 8);
    }

    // remove all lessons, which appear as related lessons in a lesson, which has been assigned more recently
    // only the most recently assigned lesson remains
    public static function filterOutRelatedLessons($mappedIds)
    {
        $result = $mappedIds;

        foreach ($mappedIds as $userLectureId => $lectureId) {
            $relatedLessonIds = RelatedLectures::getRelations($lectureId);

            if (!empty($relatedLessonIds)) {
                $largestRelatedLessonId = max(array_keys($relatedLessonIds));
                $mostRecentUserLessonId = $largestRelatedLessonId > $userLectureId
                    ? $largestRelatedLessonId
                    : $lectureId;

                foreach ($relatedLessonIds as $relatedLessonId => $lessonId) {
                    $userLessonId = array_search((int)$lessonId, $result);
                    if ($relatedLessonId != $mostRecentUserLessonId && $userLessonId !== false) {
                        unset($result[$userLessonId]);
                    }
                }
            }
        }

        return $result;
    }

    public static function filterOutOldOpenedLessons($userLessons)
    {
        foreach ($userLessons as $id => $userLesson) {
            if ($userLesson['opened'] && $userLesson['created'] < self::STILL_LEARNING_TRANSITION_DATE) {
                unset($userLessons[$id]);
            }
        }

        return $userLessons;
    }

    public static function getUnsentLectures($id, $evaluated = 0, $sent = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated, 'sent' => $sent])->orderBy(['id' => SORT_DESC])->all();
    }

    public static function setSeenByUser($user_id, $id)
    {
        $setOpenTime = false;
        if (($user = Users::findOne($user_id)) !== null) {
            $model = self::find()->where(['opened' => 0, 'user_id' => $user_id, 'lecture_id' => $id])->one();
            if ($model) {
                $model->opened = 1;
                if (!$setOpenTime) {
                    $model->open_times = $model->open_times + 1;
                }
                $model->opentime = date('Y-m-d H:i:s', time());
                $setOpenTime = $model->update();
            } else {
                $model = self::find()->where(['opened' => 1, 'user_id' => $user_id, 'lecture_id' => $id])->one();
                if ($model && !$setOpenTime) {
                    $model->open_times = (int) $model->open_times + 1;
                    $setOpenTime = $model->update();
                }
            }
            $user->last_lecture = $id;
            $user->update();
        }
        return true;
    }

    public static function getOpened($id)
    {
        $results = self::find()->where(['user_id' => $id, 'opened' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'id') : [];
    }

    public static function getNewLectures($user_id, $ids = []): array
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

    public static function getUserLectureByLectureId($lecture_id)
    {
        $user_id = Yii::$app->user->identity->id;

        return self::findOne(['user_id' => $user_id, 'lecture_id' => $lecture_id]);
    }

    public static function getFirstOpentime($user_id)
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


    public static function getNextLessonId($studentId, $currentLectureId, $type)
    {
        $lectureIds = self::getLessonsOfType($studentId, $type);
        $lectures = Lectures::find()->where(['in', 'id', $lectureIds])->orderBy(['title' => SORT_ASC])->asArray()->all();

        $takeNext = false;
        foreach ($lectures as $lecture) {
            if ($takeNext) {
                return $lecture['id'];
            }
            if ($lecture["id"] === $currentLectureId) {
                $takeNext = true;
            }
        }
    }

    public static function sendEmail($id, $subject, $teacherMessage = null)
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

    public static function sendAdminEmail($id, $lecture_id, $x)
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

    public static function getUnassignedLectures($id){

        $school = School::getByStudent($id);
        $schoolLectures = SchoolLecture::getSchoolLectureIds($school);
        $userLectures = self::getUserLectures($id);
        $unassigned = [];
        foreach ($schoolLectures as $slecture) {
            $new = true;
            foreach($userLectures as $ulecture) {
                if ($slecture == $ulecture) {$new = false; break;}
            }
            if ($new) array_push($unassigned, Lectures::findOne($slecture));
        }
        return $unassigned;
    }

    public static function getLastThreeComplexityAverage($id) {
        $lastLectures = self::getLastLecturesForUser($id,3);
        $avg = 0;
        foreach ($lastLectures as $lectureId) {
            $lecture = Lectures::findOne($lectureId);
            $avg = $avg + $lecture->complexity;
        }
        $avg = (int)round($avg / 3, 0);
        return $avg;
    }
}
