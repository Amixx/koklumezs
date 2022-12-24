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
use app\models\LessonAssignmentMessages;
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
        $userContext = Yii::$app->user->identity;
        $school = $userContext->getSchool();
        $isFitnessSchool = $school->is_fitness_school;

        $options = [];
        if ($userContext->isTeacher()) {
            $users = Users::getStudentsWithoutPausesForSchool();
        } else {
            $users = Users::getStudents();
        }

        $evaluations = [];
        $goals = [];
        $lastUserlectures = [];
        $videoParam = Evaluations::getVideoParam();
        foreach ($users as $id => $e) {
            $lastUserlectures[$id] = UserLectures::getLastEvaluatedLecture($id);
            if ($lastUserlectures[$id]) {
                if (!isset($evaluations[$id])) {
                    foreach ($lastUserlectures[$id]->lecture->userLectureEvaluations as $ule) {
                        if ($ule['evaluation_id'] === 1 && $ule['user_id'] == $id) {
                            $evaluations[$id][$lastUserlectures[$id]->lecture_id] = $ule['evaluation'];
                        }
                    }
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
        $options['lastlectures'] = $lastUserlectures;
        $options['isFitnessSchool'] = $isFitnessSchool;

        return $this->render('index', $options);
    }

    public function actionUserlectures($id = null)
    {
        $userContext = Yii::$app->user->identity;
        $isTeacher = $userContext->isTeacher();
        $school = $userContext->getSchool();
        $isFitnessSchool = $school->is_fitness_school;

        $this->view->params['chatRecipientId'] = $id;

        if ($id == null) {
            $users = $isTeacher
                ? Users::getStudentsWithoutPausesForSchool()
                : $users = Users::getStudents();
            $id = key($users);
        }

        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        $user = Users::findOne($id);
        $goals = StudentGoals::getUserGoals($id);
        $diff = Studentgoals::getUserDifficulty($id);
        $goalsnow = StudentGoals::NOW;
        $goalsum = isset($goals[$goalsnow]) ? array_sum($goals[$goalsnow]) : 0;
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

                $shouldSaveEmail = isset($post['saveEmail']) && $post['saveEmail'];
                if ($shouldSaveEmail) {
                    $saved = LessonAssignmentMessages::createFrom($model->lecture_id, $post);
                    if ($saved) {
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Automatic message saved') . '!');
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Could not save automatic message') . '!');
                    }
                }

                $shouldUpdateEmail = isset($post['updateEmail']) && $post['updateEmail'];
                if ($shouldUpdateEmail) {
                    $updated = LessonAssignmentMessages::updateWith($model->lecture_id, $post);
                    if ($updated) {
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Automatic message updated') . '!');
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Could not update automatic message') . '!');
                    }
                }

                $shouldSetWeight = isset($post['weight']) && $post['weight'];
                if ($shouldSetWeight) {
                    $model->weight = $post['weight'];
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
        $lastUserlectures = [];
        $videoParam = Evaluations::getVideoParam();
        $lastUserlectures = UserLectures::getAllLectures($id);
        $sevenDayResult = UserLectures::getDayResult($id, 7);
        $thirtyDayResult = UserLectures::getDayResult($id, 30);
        $userLectures = UserLectures::getUserLectures($id);
        $lectures = Lectures::getLecturesObjectsForUser($userLectures);

        if ($lastUserlectures) {
            foreach ($lastUserlectures as $uLecture) {
                foreach ($uLecture->lecture->userLectureEvaluations as $ule) {
                    if ($ule['evaluation_id'] === 1 && $ule['user_id'] == $id) {
                        $evaluations[$uLecture->lecture_id] = $ule['evaluation'];
                    }
                }
            }
        }

        $filterLang = array_key_exists("lang", $get) ? $get["lang"] : null;
        $filterSubTypes = (array_key_exists("subTypes", $get) && isset($get["subTypes"]) && $get["subTypes"] !== '') ? explode(",", $get["subTypes"]) : null;
        $users = Users::getStudentsWithParams($filterLang, $filterSubTypes);

        if ($isTeacher) {
            $schoolId = $userContext->getSchool()->id;
            $schoolLectureIds = SchoolLecture::getSchoolLectureIds($schoolId);
            $schoolStudentIds = SchoolStudent::getSchoolStudentIds($schoolId);

            $users = array_filter($users, function ($user) use ($schoolStudentIds) {
                return in_array($user["id"], $schoolStudentIds);
            });
            $lectures = array_filter($lectures, function ($lecture) use ($schoolLectureIds) {
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

        $firstEvaluationDate = UserLectureEvaluations::getFirstDifficultyEvaluationDate($currentUserId);

        $openTimes['seven'] = LectureViews::getDayResult($currentUserId, 7);
        $openTimes['thirty'] = LectureViews::getDayResult($currentUserId, 30);

        $trialEnded = Trials::displayTrialEndedMessage($currentUserId);

        $options['id'] = $id;
        $options['videoparamtexts'] = $videotexts;
        $options['user'] = $user;
        $options['videoParam'] = $videoParam;
        $options['evaluations'] = $evaluations;
        $options['lastlectures'] = $lastUserlectures;
        $options['sevenDayResult'] = $sevenDayResult;
        $options['thirtyDayResult'] = $thirtyDayResult;
        $options['manualLectures'] = $lectures;
        $options['model'] = new UserLectures;
        $options['diff'] = $diff;
        $options['prevUserId'] = $prevUserId;
        $options['nextUserId'] = $nextUserId;
        $options['filterLang'] = $filterLang;
        $options['filterSubTypes'] = isset($filterSubTypes) ? implode(",", $filterSubTypes) : null;
        $options['currentUserIndex'] = $currentUserKey;
        $options['userCount'] = $userCount;
        $options['firstEvaluationDate'] = $firstEvaluationDate;
        $options['openTimes'] = $openTimes;
        $options['trialEnded'] = $trialEnded;
        $options['isFitnessSchool'] = $isFitnessSchool;

        return $this->render('userlectures', $options);
    }
}
