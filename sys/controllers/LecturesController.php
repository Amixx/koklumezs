<?php

namespace app\controllers;

use app\models\Users;
use app\models\Lectures;
use app\models\Evaluations;
use app\models\Difficulties;
use app\models\Lecturesfiles;
use app\models\LecturesSearch;
use app\models\TeacherLecturesSearch;
use app\models\RelatedLectures;
use app\models\Lecturesevaluations;
use app\models\LecturesDifficulties;
use app\models\SchoolLecture;
use app\models\SchoolTeacher;
use app\models\School;


use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LecturesController implements the CRUD actions for Lectures model.
 */
class LecturesController extends Controller
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
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $isCurrentUserTeacher = Users::isCurrentUserTeacher();
        $searchModel = $isCurrentUserTeacher ? new TeacherLecturesSearch() : new LecturesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        $admins = Users::getAdmins();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get,
            'admins' => $admins,

        ]);
    }

    /**
     * Displays a single Lectures model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $schoolId = School::getCurrentSchoolId();
        $difficulties = Difficulties::getDifficultiesForSchool($schoolId);
        $evaluations = Evaluations::getEvaluations();
        $lectures = SchoolLecture::getSchoolLectureTitles($schoolId);
        $post = Yii::$app->request->post();
        $model = new Lectures();
        $model->author = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s', time());
        $model->complexity = 1;
        if (isset($post['difficulties']) && isset($post['difficultiesSelected'])) {
            $selectedDifficultiesCount = count($post['difficultiesSelected']);
            $sum = 0;
            foreach ($post['difficulties'] as $pid => $value) {
                $difficultySelected = isset($post['difficultiesSelected'][$pid]) && $post['difficultiesSelected'][$pid];
                if ($difficultySelected) {
                    $value = $value ?? 0;
                    if (is_numeric($value)) {
                        $sum += (10 * pow(2, ($value / 3)));
                    }
                }
            }
            $model->complexity = round($sum / $selectedDifficultiesCount);
        }

        if ($model->load($post) && $model->save()) {
            if (Users::isCurrentUserTeacher()) {
                $newSchoolLecture = new SchoolLecture();
                $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
                $newSchoolLecture->school_id = $currentUserTeacher->school_id;
                $newSchoolLecture->lecture_id = $model->id;
                $newSchoolLecture->save();
            }
            if (isset($post['difficulties']) && isset($post['difficultiesSelected'])) {
                foreach ($post['difficulties'] as $pid => $value) {
                    $difficultySelected = isset($post['difficultiesSelected'][$pid]) && $post['difficultiesSelected'][$pid];
                    if ($difficultySelected) {
                        $difficulty = new LecturesDifficulties();
                        $difficulty->diff_id = $pid;
                        $difficulty->lecture_id = $model->id;
                        $difficulty->value = $value ?? 0;
                        if (is_numeric($difficulty->value)) {
                            $sum += $difficulty->value;
                        }
                        $difficulty->save();
                    }
                }
            }
            foreach ($evaluations as $eval) {
                $evaluation = new Lecturesevaluations();
                $evaluation->evaluation_id = (int) $eval['id'];
                $evaluation->lecture_id = $model->id;
                $evaluation->save();
            }
            if (isset($post['relatedLectures'])) {
                foreach ($post['relatedLectures'] as $rid) {
                    $relation = new RelatedLectures();
                    $relation->related_id = $rid;
                    $relation->lecture_id = $model->id;
                    $relation->save();
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'difficulties' => $difficulties,
            'evaluations' => $evaluations,
            'lectures' => $lectures,
        ]);
    }

    public function actionUpdate($id)
    {
        $schoolId = School::getCurrentSchoolId();
        $post = Yii::$app->request->post();
        $difficulties = Difficulties::getDifficultiesForSchool($schoolId);
        $evaluations = Evaluations::getEvaluations();
        $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
        $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
        $lecturefiles = Lecturesfiles::getLectureFiles($id);
        $relatedLectures = RelatedLectures::getRelations($id);
        $lectures = SchoolLecture::getSchoolLectureTitles($schoolId);
        $model = $this->findModel($id);
        $model->updated = date('Y-m-d H:i:s', time());
        $model->complexity = 1;
        if (isset($post['difficulties']) && isset($post['difficultiesSelected'])) {
            $selectedDifficultiesCount = count($post['difficultiesSelected']);
            $sum = 0;
            foreach ($post['difficulties'] as $pid => $value) {
                $difficultySelected = isset($post['difficultiesSelected'][$pid]) && $post['difficultiesSelected'][$pid];
                if ($difficultySelected) {
                    $value = $value ?? 0;
                    if (is_numeric($value)) {
                        $sum += (10 * pow(2, ($value / 3)));
                    }
                }
            }
            $model->complexity = round($sum / $selectedDifficultiesCount);
        }
        if ($model->load($post) && $model->save()) {
            if (isset($post['difficulties'])) {
                LecturesDifficulties::removeLectureDifficulties($model->id);
                foreach ($post['difficulties'] as $pid => $value) {
                    $difficultySelected = isset($post['difficultiesSelected'][$pid]) && $post['difficultiesSelected'][$pid];

                    if ($difficultySelected) {
                        $difficulty = new LecturesDifficulties();
                        $difficulty->diff_id = $pid;
                        $difficulty->lecture_id = $model->id;
                        $difficulty->value = $value ?? 0;
                        $difficulty->save();
                    }
                }
                $model->updated = date('Y-m-d H:i:s', time());
                $model->save(false);
            }
            if (isset($post['evaluations'])) {
                Lecturesevaluations::removeLectureEvalutions($id);
                foreach ($post['evaluations'] as $eid => $value) {
                    $evaluation = new Lecturesevaluations();
                    $evaluation->evaluation_id = $eid;
                    $evaluation->lecture_id = $model->id;
                    $evaluation->save();
                }
            }
            RelatedLectures::removeLectureRelations($id);
            if (isset($post['relatedLectures'])) {
                foreach ($post['relatedLectures'] as $rid) {
                    $relation = new RelatedLectures();
                    $relation->related_id = $rid;
                    $relation->lecture_id = $model->id;
                    $relation->save();
                }
            }

            return $this->redirect(Url::previous());
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('update', [
            'model' => $model,
            'difficulties' => $difficulties,
            'evaluations' => $evaluations,
            'lectureDifficulties' => $lectureDifficulties,
            'lectureEvaluations' => $lectureEvaluations,
            'lecturefiles' => $lecturefiles,
            'relatedLectures' => $relatedLectures,
            'lectures' => $lectures,
        ]);
    }

    /**
     * Deletes an existing Lectures model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        self::deleteAllRelatedForLecture('lecturesevaluations', $id);
        self::deleteAllRelatedForLecture('userlectureevaluations', $id);
        self::deleteAllRelatedForLecture('userlectures', $id);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    private static function deleteAllRelatedForLecture($tablename, $id)
    {
        Yii::$app
            ->db
            ->createCommand()
            ->delete($tablename, ['lecture_id' => $id])
            ->execute();
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
