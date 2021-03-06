<?php

namespace app\controllers;

use app\models\Evaluations;
use app\models\Lectures;
use app\models\LectureAssignment;
use app\models\LecturesDifficulties;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\SchoolLecture;
use app\models\SchoolTeacher;
use app\models\SchoolStudent;
use app\models\LectureViews;
use app\models\RelatedLectures;
use app\models\StudentSubPlans;
use app\models\Trials;
use Yii;
use yii\web\Controller;

class AssignController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $options = [];
        if (Users::isCurrentUserTeacher()) {
            $users = Users::getStudentsWithoutPausesForSchool();
        } else {
            $users = Users::getStudents();
        }

        $evaluations = [];
        $lectureDifficulties = [];
        $goals = [];
        $lastlectures = [];
        $videoParam = Evaluations::getVideoParam();
        $evaluationsTitles = Evaluations::getEvaluationsTitles();
        $evaluationsValues = Evaluations::getEvaluationsValueTexts();
        foreach ($users as $id => $e) {
            $lastlectures[$id] = UserLectures::getLastEvaluatedLecture($id);
            if ($lastlectures[$id]) {
                if (!isset($evaluations[$id])) {
                    $evaluations[$id] = Userlectureevaluations::getLectureEvaluations($id, $lastlectures[$id]['lecture_id']);
                }
                if (!isset($lectureDifficulties[$lastlectures[$id]['lecture_id']])) {
                    $lectureDifficulties[$lastlectures[$id]['lecture_id']] = LecturesDifficulties::getLectureDifficulties($lastlectures[$id]['lecture_id']);
                }
            }
            if (!isset($goals[$id])) {
                $goals[$id] = StudentGoals::getUserGoals($id);
            }
        }

        $videotexts = unserialize($videoParam->star_text);
        $options['videoparamtexts'] = $videotexts;
        $options['goalsnow'] = StudentGoals::NOW;
        $options['users'] = $users;
        $options['goals'] = $goals;
        $options['videoParam'] = $videoParam;
        $options['evaluations'] = $evaluations;
        $options['lastlectures'] = $lastlectures;
        $options['lectureDifficulties'] = $lectureDifficulties;
        $options['evaluationsTitles'] = $evaluationsTitles;
        $options['evaluationsValues'] = $evaluationsValues;

        return $this->render('index', $options);
    }

    public function actionUserlectures($id = null)
    {

        $this->view->params['chatRecipientId'] = $id;

        if ($id == null) {
            if (Users::isCurrentUserTeacher()) {
                $users = Users::getStudentsWithoutPausesForSchool();
            } else {
                $users = Users::getStudents();
            }
            $id = key($users);
        }

        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        $user = Users::findOne($id);
        $goals = StudentGoals::getUserGoals($id);
        $diff = Studentgoals::getUserDifficulty($id);
        $goalsnow = StudentGoals::NOW;
        $goalsum = isset($goals[$goalsnow]) ? array_sum($goals[$goalsnow]) : 0;
        $endDate = StudentSubPlans::getLearningPlanEndDateString($id);
        if ($post) {
            $model = new UserLectures();
            $model->assigned = Yii::$app->user->identity->id;
            $model->created = date('Y-m-d H:i:s', time());
            $model->user_difficulty = $goalsum;
            if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
                $shouldSendEmail = isset($post['sendEmail']) && $post['sendEmail'];
                if ($shouldSendEmail) {
                    $subject = isset($post['subject']) && $post['subject'] ? $post['subject'] : "Jaunas nodarbības";
                    $teacherMessage = $post['teacherMessage'];
                    UserLectures::sendEmail($model->user_id, $subject, $teacherMessage);
                }

                $user->wants_more_lessons = false;
                $user->update();
                $model->sent = 1;
                $model->update();
                return $this->refresh();
            }
        }
        if (isset($get['assign']) && is_numeric($get['assign'])) {
            $model = new UserLectures();
            $model->assigned = Yii::$app->user->identity->id;
            $model->created = date('Y-m-d H:i:s', time());
            $model->user_id = $id;
            $model->lecture_id = $get['assign'];
            $model->user_difficulty = $goalsum;
            $saved = $model->save();
            if ($saved) {
                $shouldSendEmail = isset($post['sendEmail']) && $post['sendEmail'];
                if ($shouldSendEmail) {
                    $teacherMessage = $post['teacherMessage'];
                    $subject = isset($post['subject']) && $post['subject'] ? $post['subject'] : "Jaunas nodarbības";
                    UserLectures::sendEmail($model->user_id, $subject, $teacherMessage);
                }

                $user->wants_more_lessons = false;
                $user->update();
                $model->sent = 1;
                $model->update();
            }
            return $this->redirect(['assign/userlectures/' . $id]);
        }
        $options = [];
        $evaluations = [];
        $lectureDifficulties = [];
        $lastlectures = [];
        $videoParam = Evaluations::getVideoParam();
        $evaluationsTitles = Evaluations::getEvaluationsTitles();
        $evaluationsValues = Evaluations::getEvaluationsValueTexts();
        $lastlectures = UserLectures::getAllLectures($id);
        $sevenDayResult = UserLectures::getDayResult($id, 7);
        $thirtyDayResult = UserLectures::getDayResult($id, 30);
        $PossibleThreeLectures = LectureAssignment::getPossibleThreeLectures($id);
        $userLectures = UserLectures::getUserLectures($id);
        $lectures = Lectures::getLecturesObjectsForUser($userLectures);

        if ($lastlectures) {
            foreach ($lastlectures as $lecture) {
                if (!isset($evaluations[$lecture->lecture_id])) {
                    $evaluations[$lecture->lecture_id] = Userlectureevaluations::getLectureEvaluations($id, $lecture->lecture_id);
                }
                if (!isset($lectureDifficulties[$lecture->lecture_id])) {
                    $lectureDifficulties[$lecture->lecture_id] = LecturesDifficulties::getLectureDifficulties($lecture->lecture_id);
                }
            }
        }

        $filterLang = array_key_exists("lang", $get) ? $get["lang"] : null;
        $filterSubTypes = (array_key_exists("subTypes", $get) && isset($get["subTypes"]) && $get["subTypes"] !== '') ? explode(",", $get["subTypes"]) : null;
        $users = Users::getStudentsWithParams($filterLang, $filterSubTypes);

        if (Users::isCurrentUserTeacher()) {
            $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
            $schoolLectureIds = SchoolLecture::getSchoolLectureIds($currentUserTeacher->school_id);
            $schoolStudentIds = SchoolStudent::getSchoolStudentIds($currentUserTeacher->school_id);

            $users = array_filter($users, function ($user) use ($schoolStudentIds) {
                return in_array($user["id"], $schoolStudentIds);
            });
            $lectures = array_filter($lectures, function ($lecture) use ($schoolLectureIds, $userLectures) {
                return in_array($lecture["id"], $schoolLectureIds);
            });
        }
        $currentUserId = $user->id;

        $userIds = array_keys($users);

        $currentUserKey = (array_search($currentUserId, $userIds, true));
        $prevUserId = key_exists($currentUserKey - 1, $userIds) ? $userIds[$currentUserKey - 1] : null;
        $nextUserId = key_exists($currentUserKey + 1, $userIds) ? $userIds[$currentUserKey + 1] : null;
        $userCount = count($users);

        $videotexts = unserialize($videoParam->star_text);

        $firstOpenTime = UserLectures::getFirstOpentime($currentUserId);

        $openTimes['seven'] = LectureViews::getDayResult($currentUserId, 7);
        $openTimes['thirty'] = LectureViews::getDayResult($currentUserId, 30);

        $nextLessons = UserLectures::getNextLessons($currentUserId);
        $isNextLessons = UserLectures::getIsNextLesson($currentUserId);

        $trialEnded = Trials::displayTrialEndedMessage($currentUserId);

        $options['id'] = $id;
        $options['videoparamtexts'] = $videotexts;
        $options['goalsum'] = $goalsum;
        $options['goalsnow'] = $goalsnow;
        $options['user'] = $user;
        $options['goals'] = $goals;
        $options['videoParam'] = $videoParam;
        $options['evaluations'] = $evaluations;
        $options['lastlectures'] = $lastlectures;
        $options['lectureDifficulties'] = $lectureDifficulties;
        $options['evaluationsTitles'] = $evaluationsTitles;
        $options['evaluationsValues'] = $evaluationsValues;
        $options['sevenDayResult'] = $sevenDayResult;
        $options['thirtyDayResult'] = $thirtyDayResult;
        $options['PossibleThreeLectures'] = $PossibleThreeLectures;
        $options['manualLectures'] = $lectures;
        $options['model'] = new UserLectures;
        $options['diff'] = $diff;
        $options['prevUserId'] = $prevUserId;
        $options['nextUserId'] = $nextUserId;
        $options['filterLang'] = $filterLang;
        $options['filterSubTypes'] = isset($filterSubTypes) ? implode(",", $filterSubTypes) : null;
        $options['currentUserIndex'] = $currentUserKey;
        $options['userCount'] = $userCount;
        $options['firstOpenTime'] = $firstOpenTime;
        $options['openTimes'] = $openTimes;
        $options['endDate'] = $endDate;
        $options['nextLessons'] = $nextLessons;
        $options['isNextLessons'] = $isNextLessons;
        $options['trialEnded'] = $trialEnded;

        return $this->render('userlectures', $options);
    }
}
