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
use app\models\SectionsVisible;
use app\models\StartLaterCommitments;
use app\models\Studentgoals;
use app\models\Trials;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
                'actions' => [],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        $models = [];
        $pages = [];
        $user = Yii::$app->user->identity;

        $videoThumb = School::getCurrentSchool()->video_thumbnail;

        if ($type) {
            $modelsIds = UserLectures::getLessonsOfType($user->id, $type);
            if ($type === "favourite") {
                $evaluatedStillLearningIds = UserLectures::getEvaluatedStillLearning($user->id);
                $modelsIds = array_merge($modelsIds, $evaluatedStillLearningIds);
            }

            if ($modelsIds) {
                $query = Lectures::find()->where(['in', 'id', $modelsIds]);
                $countQuery = clone $query;
                $pages = new Pagination(['totalCount' => $countQuery->count()]);

                $SortByDifficulty = Yii::$app->request->get('sortByDifficulty');

                if (!(isset($SortByDifficulty)) || $SortByDifficulty == '' || $SortByDifficulty == 'desc') {
                    $sortByDifficulty = 'asc';
                    $orderBy = ['lectures.complexity' => SORT_ASC];
                } else {
                    $sortByDifficulty = 'desc';
                    $orderBy = ['lectures.complexity' => SORT_DESC];
                }

                $models = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->orderBy($orderBy)
                    ->all();

                $opened = UserLectures::getOpened($user->id);
                $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);

                $title_filter = Yii::$app->request->get('title_filter');

                if ($title_filter) {
                    $models = array_filter($models, function ($item) use ($title_filter) {
                        $title_lower = mb_strtolower(trim($item->title), 'UTF-8');
                        return strpos($title_lower, $title_filter) !== false;
                    });
                }

                return $this->render('index', [
                    'models' => $models,
                    'type' => $type,
                    'opened' => $opened,
                    'pages' => $pages,
                    'userLectureEvaluations' => $userLectureEvaluations,
                    'videoThumb' => $videoThumb,
                    'sortByDifficulty' => $sortByDifficulty,
                    'title_filter' => $title_filter,

                ]);
            }
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
        $user = Yii::$app->user->identity;
        $dbUser = Users::findOne([$id => $user->id]);

        $videoThumb = School::getCurrentSchool()->video_thumbnail;

        $SortByDifficulty = Yii::$app->request->get('sortByDifficulty');

        if (!(isset($SortByDifficulty)) || $SortByDifficulty == '' || $SortByDifficulty == 'desc') {
            $sortByDifficulty = 'asc';
            $orderBy = ['lectures.complexity' => SORT_ASC];
        } else {
            $sortByDifficulty = 'desc';
            $orderBy = ['lectures.complexity' => SORT_DESC];
        }

        $force = Yii::$app->request->get('force');
        $userLectures = $force ? [] : UserLectures::getLectures($user->id, $SortByDifficulty);
        $modelsIds = $force ? [$id] : UserLectures::getUserLectures($user->id); //UserLectures::getSentUserLectures($user->id)
        $check = in_array($id, $modelsIds);
        $userEvaluatedLectures = $force ? [] : UserLectures::getEvaluatedLectures($user->id);

        $nextLessonId = null;
        $userLecture = UserLectures::findOne(['user_id' => $user->id, 'lecture_id' => $id]);

        if ($userLecture) {
            $type = $userLecture->is_favourite ? "favourite" : "new";
            $nextLessonId = UserLectures::getNextLessonId($user->id, $id, $type);
        }

        $difficultyEvaluation = $force ? null : Userlectureevaluations::getLecturedifficultyEvaluation($user->id, $id);

        if ($check) {
            $post = Yii::$app->request->post();
            if (isset($post["difficulty-evaluation"])) {
                if ($difficultyEvaluation) {
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->update();
                } else {
                    $shouldStartTrial = $model->complexity > 1 && !Userlectureevaluations::hasAnyLegitEvaluations($user->id);

                    if ($shouldStartTrial) {
                        $trial = Trials::find()->where(['user_id' => $user->id])->one();

                        if (!$trial) {
                            $trial = new Trials;
                            $trial->user_id = $user->id;
                            $trial->save();

                            $dbUser->status = 10;
                            $dbUser->save();
                        }
                    }


                    $difficultyEvaluation = new Userlectureevaluations();
                    $difficultyEvaluation->evaluation_id = 1;
                    $difficultyEvaluation->lecture_id = $model->id;
                    $difficultyEvaluation->user_id = $user->id;
                    $difficultyEvaluation->created = date('Y-m-d H:i:s', time());
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->public_comment = false;
                    $difficultyEvaluation->save();

                    $schoolId = School::getCurrentSchool()->id;
                    $teacherId = SchoolTeacher::getByCurrentStudent()->user_id;
                    $message = SchoolAfterEvaluationMessages::getRandomMessage($schoolId, $post["difficulty-evaluation"]);

                    if ($message) {
                        Chat::addNewMessage($message, $teacherId, $user->id, 2);
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
                UserLectures::setSeenByUser($user->id, $id);
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

            $latestNewLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "new");
            $latestFavouriteLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "favourite");
            $newLessons = Lectures::find()->where(['in', 'id', $latestNewLecturesIds])->orderBy($orderBy)->all();
            $favouriteLessons = Lectures::find()->where(['in', 'id', $latestFavouriteLecturesIds])->orderBy($orderBy)->all();

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
                    $lectureView->user_id = $user->id;
                    $lectureView->lecture_id = $id;
                    $lectureView->save();
                }
            }

            if ($model->complexity > 5) {
                $startLaterCommitment = StartLaterCommitments::findOne(['user_id' => $user['id']]);
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
                'newLessons' => $newLessons,
                'favouriteLessons' => $favouriteLessons,
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
                'sortByDifficulty' => $sortByDifficulty,
                'isRegisteredAndNewLesson' => $isRegisteredAndNewLesson,
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
        $videoThumb = School::getCurrentSchool()->video_thumbnail;

        return $this->renderOverview($user, $models, $pages, $videoThumb);
    }

    private function renderOverview($user, $models, $pages, $videoThumb)
    {
        $latestNewLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "new");
        $latestFavouriteLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "favourite");
        $schoolStudent = SchoolStudent::getSchoolStudent($user->id);

        $newLessonsQuery = Lectures::find()->where(['in', 'id', $latestNewLecturesIds]);
        if (!$schoolStudent['show_real_lessons']) {
            $newLessonsQuery->andWhere(['<', 'complexity', 5]);
        }

        $newLessons = $newLessonsQuery->all();
        $favouriteLessons = Lectures::find()->where(['in', 'id', $latestFavouriteLecturesIds])->all();

        function sortFunc($a, $b, $lessonIds)
        {
            $aId = array_search($a['id'], $lessonIds);
            $bId = array_search($b['id'], $lessonIds);

            return ($aId < $bId) ? -1 : 1;
        }

        $newSortFunc = function ($a, $b) use ($latestNewLecturesIds) {
            return sortFunc($a, $b, $latestNewLecturesIds);
        };
        $favSortFunc = function ($a, $b) use ($latestFavouriteLecturesIds) {
            return sortFunc($a, $b, $latestFavouriteLecturesIds);
        };

        usort($newLessons, $newSortFunc);
        usort($favouriteLessons, $favSortFunc);

        $opened = UserLectures::getOpened($user->id);
        $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);

        $title_filter = 1;

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
            'newLessons' => $newLessons,
            'favouriteLessons' => $favouriteLessons,
            'opened' => $opened,
            'pages' => $pages,
            'userLectureEvaluations' => $userLectureEvaluations,
            'videoThumb' => $videoThumb,
            'nextLessons' => $nextLessons,
            'isNextLesson' => $isNextLesson,
            'renderRequestButton' => !$user->wants_more_lessons,
            'isActive' => $isActive,
        ]);
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
