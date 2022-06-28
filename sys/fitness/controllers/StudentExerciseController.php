<?php

namespace app\fitness\controllers;

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
use app\models\SchoolTeacher;
use app\models\Chat;
use app\models\SchoolAfterEvaluationMessages;
use app\models\SectionsVisible;
use app\models\StartLaterCommitments;
use app\models\Trials;
use app\fitness\models\WorkoutExercise;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StudentExerciseController extends Controller
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

    public function actionView($id)
    {
        $userContext = Yii::$app->user->identity;
        $dbUser = Users::findOne([$id => $userContext->id]);
        $school = $userContext->getSchool();
        $schoolId = $school->id;
        $videoThumb = $school->video_thumbnail;
        $isFitnessSchool = true;

        $workoutExercise = $this->findModel($id);
        $nextWorkoutExercise = $workoutExercise->workout->getNextWorkoutExercise($workoutExercise);

        // $difficultyEvaluation = $force ? null : Userlectureevaluations::getLecturedifficultyEvaluation($userContext->id, $id);

        $post = Yii::$app->request->post();
        if (isset($post["difficulty-evaluation"])) {
            // handle workout exercise evaluation
        }

        // UserLectures::setSeenByUser($userContext->id, $id);

        // $difficulties = Difficulties::getDifficulties();
        // $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
        // $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
        // $lecturefiles = Lecturesfiles::getLectureFiles($id);
        // $hasEvaluatedLesson = $difficultyEvaluation !== null;
        // $relatedLessonIds = RelatedLectures::getRelations($id);

        // $userCanDownloadFiles = $dbUser->allowed_to_download_files;
        // $relatedLectures = Lectures::getLecturesByIds($relatedLessonIds);
        // $difficultiesVisible = SectionsVisible::isVisible("Nodarbības sarežģītība");

        // $latestNewUserLessons = UserLectures::getLatestLessonsOfType($userContext->id, "new");
        // $latestFavouriteUserLessons = UserLectures::getLatestLessonsOfType($userContext->id, "favourite");

        // $isStudent = Yii::$app->user->identity->user_level == 'Student';
        // $previousUrl = Yii::$app->request->referrer;
        // if ($isStudent && $previousUrl) {
        //     if (strpos($previousUrl, "?") !== false) {
        //         $previousUrl = strstr($previousUrl, '?', true); // noņem query params
        //     }

        //     $previousUrlSplit = explode("/", $previousUrl);
        //     $lastUrlPart = end($previousUrlSplit);
        //     $isDifferentLesson = $lastUrlPart !== $id;

        //     if ($isDifferentLesson) {
        //         $lectureView = new LectureViews;
        //         $lectureView->user_id = $userContext->id;
        //         $lectureView->lecture_id = $id;
        //         $lectureView->save();
        //     }
        // }

        // if ($model->complexity > 5) {
        //     $startLaterCommitment = StartLaterCommitments::findOne(['user_id' => $userContext['id']]);
        //     if ($startLaterCommitment && !$startLaterCommitment['commitment_fulfilled']) {
        //         $startLaterCommitment['commitment_fulfilled'] = true;
        //         $startLaterCommitment->update();
        //     }
        // }

        // $nextRoundLessonsEquipmentVideos = [];

        // if ($isFitnessSchool && $userLecture->lecture->is_pause) {
        //     $matchDate = date("Y-m-d", strtotime($userLecture->created));
        //     $userLessons = UserLectures::getLessonsOfType($userContext->id, $type, ['id' => SORT_ASC])->all();

        //     $userLessonsForDate = [];
        //     foreach ($userLessons as $userLesson) {
        //         if (date("Y-m-d", strtotime($userLesson->created)) == $matchDate) {
        //             $userLessonsForDate[] = $userLesson;
        //         }
        //     }

        //     $useLessons = false;
        //     foreach ($userLessonsForDate as $userLesson) {
        //         if ($userLesson->id == $userLecture->id) $useLessons = true;
        //         else if ($useLessons) {
        //             if ($userLesson->lecture->is_pause) {
        //                 $useLessons = false;
        //             } else if ($userLesson->lecture->play_along_file) {
        //                 $nextRoundLessonsEquipmentVideos[] = $userLesson->lecture->play_along_file;
        //             }
        //         }
        //     }
        // }

        // $isRegisteredAndNewLesson = RegistrationLesson::isRegistrationLesson($model->id);

        return $this->render('@app/fitness/views/student-exercise/view', [
            'workoutExercise' => $workoutExercise,
            'nextWorkoutExercise' => $nextWorkoutExercise,
            'videoThumb' => $videoThumb,
        ]);



        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleIsFavourite($lectureId)
    {
        $model = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => Yii::$app->user->identity->id]);
        $model->is_favourite = !$model->is_favourite;
        $model->update();
        return $this->redirect(Yii::$app->request->referrer);
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
        if (($model = WorkoutExercise::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
