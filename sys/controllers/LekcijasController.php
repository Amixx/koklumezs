<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\LectureViews;
use app\models\RelatedLectures;
use app\models\RegistrationLesson;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\Chat;
use app\models\SchoolAfterEvaluationMessages;
use app\models\SchoolStudent;
use app\models\SchoolSubPlans;
use app\models\SectionsVisible;
use app\models\StartLaterCommitments;
use app\models\Studentgoals;
use app\models\StudentSubPlans;
use app\models\Trials;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class LekcijasController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !empty(Yii::$app->user->identity);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'requestDifferentLesson' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed    
     */
    public function actionIndex($type = null)
    {
        $get = Yii::$app->request->get();
        $userContext = Yii::$app->user->identity;

        $alreadyRecirected = isset($get['recommend_subscription_plans']);
        $hasAnyActiveLessonPlans = StudentSubPlans::userHasAnyActiveLessonPlans($userContext->id);
        $isFreeUser = $userContext['subscription_type'] === 'free';
        $trialEnded = Trials::trialEnded($userContext['id']);

        if (!$hasAnyActiveLessonPlans && !$alreadyRecirected && !$isFreeUser && $trialEnded) {
            if ($type) {
                return $this->redirect("?type=$type&recommend_subscription_plans=1");
            } else {
                return $this->redirect("?recommend_subscription_plans=1");
            }
        }

        $models = [];
        $pages = [];
        $user = Yii::$app->user->identity;
        $school = $user->getSchool();
        $isFitnessSchool = $school->is_fitness_school;
        $videoThumb = $school->video_thumbnail;

        if ($type) {
            $title_filter = Yii::$app->request->get('title_filter');
            $sortingConfig = $this->getSortingConfig();

            $userLessonsQuery = UserLectures::getLessonsOfType($user->id, $type, $sortingConfig['orderBy']);
            $countQuery = clone $userLessonsQuery;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);

            $models = $userLessonsQuery->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);

            if ($title_filter) {
                $models = array_filter($models, function ($item) use ($title_filter) {
                    $title_lower = mb_strtolower(trim($item->title), 'UTF-8');
                    return strpos($title_lower, $title_filter) !== false;
                });
            }

            $modelGroups = null;

            if ($isFitnessSchool) {
                $modelGroups = [];
                foreach ($models as $model) {
                    $modelGroupDate = date("Y-m-d", strtotime($model->created));
                    $modelGroups[$modelGroupDate][] = $model;
                }

                foreach ($modelGroups as &$modelGroup) {
                    usort($modelGroup, function ($a, $b) {
                        return $a->id > $b->id;
                    });
                }

                return $this->render('index', [
                    'models' => $models,
                    'modelGroups' => $modelGroups,
                    'type' => $type,
                    'pages' => $pages,
                    'userLectureEvaluations' => $userLectureEvaluations,
                    'videoThumb' => $videoThumb,
                    'title_filter' => $title_filter,
                ]);
            }

            return $this->render('index', [
                'models' => $models,
                'type' => $type,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'videoThumb' => $videoThumb,
                'sortType' => $sortingConfig['type'],
                'title_filter' => $title_filter,

            ]);
        } else {
            return $this->renderOverview($user, $models, $pages, $videoThumb);
        }

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionLekcija($id)
    {
        $model = $this->findModel($id);
        $userContext = Yii::$app->user->identity;
        $dbUser = Users::findOne([$id => $userContext->id]);
        $school = $userContext->getSchool();
        $schoolId = $school->id;
        $videoThumb = $school->video_thumbnail;

        $sortingConfig = $this->getSortingConfig();

        $force = Yii::$app->request->get('force');
        $userLectures = $force ? [] : UserLectures::getLectures($userContext->id, $sortingConfig['orderBy']);
        $modelsIds = $force ? [$id] : UserLectures::getUserLectures($userContext->id); //UserLectures::getSentUserLectures($userContext->id)
        $check = in_array($id, $modelsIds);
        $userEvaluatedLectures = $force ? [] : UserLectures::getEvaluatedLectures($userContext->id);

        $nextLessonId = null;
        $userLecture = UserLectures::findOne(['user_id' => $userContext->id, 'lecture_id' => $id]);

        if ($userLecture) {
            $type = $userLecture->is_favourite ? "favourite" : "new";
            $nextLessonId = UserLectures::getNextLessonId($userContext->id, $userLecture, $type);
        }

        $difficultyEvaluation = $force ? null : Userlectureevaluations::getLecturedifficultyEvaluation($userContext->id, $id);

        if ($check) {
            $post = Yii::$app->request->post();
            if (isset($post["difficulty-evaluation"])) {
                if ($difficultyEvaluation) {
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->update();
                } else {
                    $shouldStartTrial = $model->complexity > 1 && !Userlectureevaluations::hasAnyLegitEvaluations($userContext->id);

                    if ($shouldStartTrial) {
                        $trial = Trials::find()->where(['user_id' => $userContext->id])->one();

                        if (!$trial) {
                            $trial = new Trials;
                            $trial->user_id = $userContext->id;
                            $trial->save();

                            $dbUser->status = 10;
                            $dbUser->save();
                        }
                    }


                    $difficultyEvaluation = new Userlectureevaluations();
                    $difficultyEvaluation->evaluation_id = 1;
                    $difficultyEvaluation->lecture_id = $model->id;
                    $difficultyEvaluation->user_id = $userContext->id;
                    $difficultyEvaluation->created = date('Y-m-d H:i:s', time());
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->public_comment = false;
                    $difficultyEvaluation->save();
                    $teacherId = SchoolTeacher::getByCurrentStudent()->user_id;
                    $message = SchoolAfterEvaluationMessages::getRandomMessage($schoolId, $post["difficulty-evaluation"]);

                    if ($message) {
                        Chat::addNewMessage($message, $teacherId, $userContext->id, 2);
                    }

                    $userLecture->evaluated = 1;
                    $userLecture->update();
                }

                $shouldRedirect = isset($post['redirect-lesson-id']) && $post['redirect-lesson-id'];
                if ($shouldRedirect) {
                    $redirectLessonId = $post['redirect-lesson-id'];
                    return $this->redirect(["lekcijas/lekcija/$redirectLessonId"]);
                }

                $this->refresh();
            }

            if (!$force) {
                UserLectures::setSeenByUser($userContext->id, $id);
            }

            $difficulties = Difficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $hasEvaluatedLesson = $difficultyEvaluation !== null;
            $relatedLessonIds = RelatedLectures::getRelations($id);

            $userCanDownloadFiles = $dbUser->allowed_to_download_files;
            $relatedLectures = Lectures::getLecturesByIds($relatedLessonIds);
            $difficultiesVisible = SectionsVisible::isVisible("Nodarbības sarežģītība");

            $latestNewUserLessons = UserLectures::getLatestLessonsOfType($userContext->id, "new");
            $latestFavouriteUserLessons = UserLectures::getLatestLessonsOfType($userContext->id, "favourite");

            $isStudent = Yii::$app->user->identity->user_level == 'Student';
            $previousUrl = Yii::$app->request->referrer;
            if ($isStudent && $previousUrl) {
                if (strpos($previousUrl, "?") !== false) {
                    $previousUrl = strstr($previousUrl, '?', true); // noņem query params
                }

                $previousUrlSplit = explode("/", $previousUrl);
                $lastUrlPart = end($previousUrlSplit);
                $isDifferentLesson = $lastUrlPart !== $id;

                if ($isDifferentLesson) {
                    $lectureView = new LectureViews;
                    $lectureView->user_id = $userContext->id;
                    $lectureView->lecture_id = $id;
                    $lectureView->save();
                }
            }

            if ($model->complexity > 5) {
                $startLaterCommitment = StartLaterCommitments::findOne(['user_id' => $userContext['id']]);
                if ($startLaterCommitment && !$startLaterCommitment['commitment_fulfilled']) {
                    $startLaterCommitment['commitment_fulfilled'] = true;
                    $startLaterCommitment->update();
                }
            }

            $isRegisteredAndNewLesson = RegistrationLesson::isRegistrationLesson($model->id);

            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                'lectureDifficulties' => $lectureDifficulties,
                'lectureEvaluations' => $lectureEvaluations,
                'lecturefiles' => $lecturefiles,
                'userLectures' => $userLectures,
                'newLessons' => $latestNewUserLessons,
                'favouriteLessons' => $latestFavouriteUserLessons,
                'userEvaluatedLectures' => $userEvaluatedLectures,
                'force' => $force,
                'relatedLectures' => $relatedLectures,
                'difficultiesVisible' => $difficultiesVisible,
                'uLecture' => $userLecture,
                'userCanDownloadFiles' => $userCanDownloadFiles,
                'videoThumb' => $videoThumb,
                'nextLessonId' => $nextLessonId,
                'hasEvaluatedLesson' => $hasEvaluatedLesson,
                'difficultyEvaluation' => $difficultyEvaluation,
                'sortType' => $sortingConfig['type'],
                'isRegisteredAndNewLesson' => $isRegisteredAndNewLesson,
                'showChangeTaskButton' => $model->complexity > 5 && !$difficultyEvaluation,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleIsFavourite($lectureId)
    {
        $model = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => Yii::$app->user->identity->id]);
        $model->is_favourite = !$model->is_favourite;
        $model->update();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPreview($studentId)
    {
        $models = [];
        $pages = [];
        $user = Users::findOne($studentId);
        $userContext = Yii::$app->user->identity;
        $videoThumb = $userContext->getSchool()->video_thumbnail;

        return $this->renderOverview($user, $models, $pages, $videoThumb, false);
    }

    private function renderOverview($user, $models, $pages, $videoThumb, $isStudent = true)
    {
        $get = Yii::$app->request->get();

        $renderPlanSuggestions = isset($get['recommend_subscription_plans']) && (int)$get['recommend_subscription_plans'] === 1;
        $paymentSuccessful = isset($get['payment_success']) && (int)$get['payment_success'] === 1;

        if ($paymentSuccessful) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Payment was successful! Thank you!'));
        }

        $planRecommendations = null;
        if ($renderPlanSuggestions) {
            $planRecommendations = SchoolSubPlans::getRecommendedPlansAfterTrial();
        }

        $latestFavouriteUserLessons = UserLectures::getLatestLessonsOfType($user->id, "favourite");
        $schoolStudent = SchoolStudent::getSchoolStudent($user->id);
        $teacherPortrait = $schoolStudent->school->teacher_portrait;

        $condition = !$schoolStudent['show_real_lessons'] ? ['<', 'complexity', 5] : null;
        $latestNewUserLessons = UserLectures::getLatestLessonsOfType($user->id, "new", $condition);

        $opened = UserLectures::getOpened($user->id);
        $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);

        $userId = Yii::$app->user->identity->id;
        $user = Users::findOne($userId);

        $post = Yii::$app->request->post();
        if ($post) {
            $goals = StudentGoals::getUserGoals($userId);
            $goalsnow = StudentGoals::NOW;
            $goalsum = isset($goals[$goalsnow]) ? array_sum($goals[$goalsnow]) : 0;

            $lessonId = Yii::$app->request->post('lessonId', null);
            $model = new UserLectures();
            $model->assigned = $userId;
            $model->created = date('Y-m-d H:i:s', time());
            $model->lecture_id = $lessonId;
            $model->user_id = $userId;
            $model->user_difficulty = $goalsum;
            $saved = $model->save();
            if ($saved) {
                $user->wants_more_lessons = true;
                $user->update();
                $model->sent = 1;
                $model->update();
            }
            return $this->redirect(['']);
        }

        $nextLessons = UserLectures::getNextLessons($userId);
        $isNextLesson = UserLectures::getIsNextLesson($userId);
        $isActive =  Users::isActive($userId);

        return $this->render('overview', [
            'models' => $models,
            'newLessons' => $latestNewUserLessons,
            'favouriteLessons' => $latestFavouriteUserLessons,
            'opened' => $opened,
            'pages' => $pages,
            'userLectureEvaluations' => $userLectureEvaluations,
            'videoThumb' => $videoThumb,
            'nextLessons' => $nextLessons,
            'isNextLesson' => $isNextLesson,
            'renderRequestButton' => !$user->wants_more_lessons,
            'isActive' => $isActive,
            'teacherPortrait' => $teacherPortrait,
            'isStudent' => $isStudent,
            'renderPlanSuggestions' => $renderPlanSuggestions,
            'planRecommendations' => $planRecommendations,
        ]);
    }


    public function actionRequestDifferentLesson($lessonId)
    {
        $lessonIdToAssign = Lectures::getLessonIdOfSimilarDifficulty($lessonId);
        $userContext = Yii::$app->user->identity;

        if ($lessonIdToAssign) {
            UserLectures::findOne(['lecture_id' => $lessonId])->delete();
            $model = new UserLectures;
            $model->assigned = $userContext->id;
            $model->created = date('Y-m-d H:i:s', time());
            $model->user_id = $userContext->id;
            $model->lecture_id = $lessonIdToAssign;
            $model->user_difficulty = 0;
            $model->save();

            Yii::$app->session->setFlash('success', Yii::t('app', 'Task changed') . '!');
            return $this->redirect(Url::to("/lekcijas/lekcija/" . $lessonIdToAssign));
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    private function getSortingConfig()
    {
        $sortType = Yii::$app->request->get('sortType');
        if (!$sortType) $sortType = 0;

        if ($sortType == 0) {
            $orderBy = ['lectures.complexity' => SORT_DESC];
        } else if ($sortType == 1) {
            $orderBy = ['lectures.complexity' => SORT_ASC];
        } else {
            $orderBy = ['id' => SORT_ASC];
        }

        return [
            'type' => $sortType,
            'orderBy' => $orderBy,
        ];
    }



    /**
     * Finds the Lectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
