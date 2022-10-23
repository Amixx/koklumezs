<?php

namespace app\fitness\controllers;

use app\fitness\models\Workout;
use app\fitness\models\PostWorkoutMessage;
use app\fitness\models\WorkoutEvaluation;
use app\models\Lectures;
use app\fitness\models\WorkoutExerciseSet;
use app\fitness\models\WorkoutExerciseSetEvaluation;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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

    public function actionIndex(){
        $user = Yii::$app->user->identity;
        $school = $user->getSchool();
        $videoThumb = $school->video_thumbnail;

//        $userLessonsQuery = UserLectures::getLessonsOfType($user->id, $type, $sortingConfig['orderBy']);
//        $countQuery = clone $userLessonsQuery;
//        $pages = new Pagination(['totalCount' => $countQuery->count()]);
//
//        $models = $userLessonsQuery->offset($pages->offset)
//            ->limit($pages->limit)
//            ->all();

        $unfinishedWorkouts = Workout::getUnfinishedForCurrentUser();
//        $unfinishedWorkoutsQuery = Workout::getForCurrentUserQuery();

        return $this->render('@app/fitness/views/student-exercise/index', [
            'unfinishedWorkouts' => $unfinishedWorkouts,
//            'pages' => $pages,
            'videoThumb' => $videoThumb,
        ]);
    }

    public function actionView($id)
    {
        $userContext = Yii::$app->user->identity;
        $school = $userContext->getSchool();
        $videoThumb = $school->video_thumbnail;

        $workoutExerciseSet = $this->findModel($id);
        $nextWorkoutExercise = $workoutExerciseSet->workout->getNextWorkoutExercise($workoutExerciseSet);
        $difficultyEvaluation = WorkoutExerciseSetEvaluation::find()->where([
            'workoutexerciseset_id' => $id,
            'user_id' => $userContext->id,
        ])->one();

        $workoutExerciseSet->workout->setAsOpened();

        $post = Yii::$app->request->post();
        if (isset($post["difficulty-evaluation"])) {
            if ($difficultyEvaluation) {
                $difficultyEvaluation->evaluation = (int)$post["difficulty-evaluation"];
            } else {
                $evaluation = new WorkoutExerciseSetEvaluation();
                $evaluation->workoutexerciseset_id = $id;
                $evaluation->user_id = $userContext->id;
                $evaluation->evaluation = (int)$post["difficulty-evaluation"];
                $evaluation->save();

                $nextWorkoutExercise
                    ? $this->redirect(['', 'id' => $nextWorkoutExercise['id']])
                    : $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('@app/fitness/views/student-exercise/view', [
            'workoutExerciseSet' => $workoutExerciseSet,
            'nextWorkoutExercise' => $nextWorkoutExercise,
            'videoThumb' => $videoThumb,
            'difficultyEvaluation' => $difficultyEvaluation,
        ]);
    }

    public function actionWorkoutSummary($workoutId)
    {
        $post = Yii::$app->request->post();

        $workout = Workout::findOne(['id' => $workoutId]);
        $messageModel = $workout->messageForCoach;
        if (!$messageModel) {
            $messageModel = new PostWorkoutMessage;
            $messageModel->workout_id = $workoutId;
        }

        if ($post && $messageModel->load($post)) {
            $video = UploadedFile::getInstance($messageModel, 'video');
            if (!is_null($video)) {
                $exploded = explode(".", $video->name);
                $ext = end($exploded);
                $messageModel->video = Yii::$app->security->generateRandomString() . ".{$ext}";
                Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/files/';
                $path = Yii::$app->params['uploadPath'] . $messageModel->video;
                $video->saveAs($path);
            }
            $audio = UploadedFile::getInstance($messageModel, 'audio');
            if (!is_null($audio)) {
                $exploded = explode(".", $audio->name);
                $ext = end($exploded);
                $messageModel->audio = Yii::$app->security->generateRandomString() . ".{$ext}";
                Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/files/';
                $path = Yii::$app->params['uploadPath'] . $messageModel->audio;
                $audio->saveAs($path);
            }

            $messageModel->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Message sent') . '!');
        }

        if (isset($post["difficulty-evaluation"])) {
            $evaluation = new WorkoutEvaluation;
            $evaluation->workout_id = $workoutId;
            $evaluation->evaluation = (int)$post["difficulty-evaluation"];
            $evaluation->save();
        }

        return $this->render('@app/fitness/views/student-exercise/workout-summary', [
            'workout' => $workout,
            'messageModel' => $messageModel,
            'workoutEvaluation' => $workout->evaluation,
            'hasBeenEvaluated' => !!$workout->evaluation,
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
        $model = WorkoutExerciseSet::find()->where(['fitness_workoutexercisesets.id' => $id])->joinWith('exerciseSet')->one();
        if (($model) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
