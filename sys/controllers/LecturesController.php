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
use app\models\Handdifficulties;
use app\models\Lecturesevaluations;
use app\models\LecturesDifficulties;
use app\models\Lectureshanddifficulties;
use app\models\SchoolLecture;
use app\models\SchoolTeacher;
use app\models\School;


use Yii;
use yii\filters\VerbFilter;
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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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

        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;

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
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Lectures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $evaluations = Evaluations::getEvaluations();
        $lectures = Lectures::getLectures();
        $post = Yii::$app->request->post();
        $model = new Lectures();
        $model->author = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s', time());
        $model->complexity = 1;
        if ($model->load($post) && $model->save()) {
            if (Users::isCurrentUserTeacher()) {
                $newSchoolLecture = new SchoolLecture();
                $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
                $newSchoolLecture->school_id = $currentUserTeacher->school_id;
                $newSchoolLecture->lecture_id = $model->id;
                $newSchoolLecture->save();
            }
            if (isset($post['difficulties'])) {
                $sum = 0;
                foreach ($post['difficulties'] as $pid => $value) {
                    $difficulty = new LecturesDifficulties();
                    $difficulty->diff_id = $pid;
                    $difficulty->lecture_id = $model->id;
                    $difficulty->value = $value ?? 0;
                    if (is_numeric($difficulty->value)) {
                        $sum += $difficulty->value;
                    }
                    $difficulty->save();
                }
                $model->complexity = (int) $sum;
                $model->update();
            }
            if (isset($post['handdifficulties'])) {
                foreach ($post['handdifficulties'] as $pid => $value) {
                    $handdifficulty = new Lectureshanddifficulties();
                    $handdifficulty->category_id = $pid;
                    $handdifficulty->lecture_id = $model->id;
                    $handdifficulty->save();
                }
            }
            if (isset($post['evaluations'])) {
                foreach ($post['evaluations'] as $eid => $value) {
                    $evaluation = new Lecturesevaluations();
                    $evaluation->evaluation_id = $eid;
                    $evaluation->lecture_id = $model->id;
                    $evaluation->save();
                }
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

        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        return $this->render('create', [
            'model' => $model,
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties,
            'evaluations' => $evaluations,
            'lectures' => $lectures,
        ]);
    }

    /**
     * Updates an existing Lectures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $difficulties = Difficulties::getDifficulties();
        $evaluations = Evaluations::getEvaluations();
        $handdifficulties = Handdifficulties::getDifficulties();
        $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
        $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
        $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
        $lecturefiles = Lecturesfiles::getLectureFiles($id);
        $relatedLectures = RelatedLectures::getRelations($id);
        $lectures = Lectures::getLecturesForRelations($id);
        $model = $this->findModel($id);
        $model->updated = date('Y-m-d H:i:s', time());
        if ($model->load($post) && $model->save()) {
            if (isset($post['difficulties'])) {
                $sum = 0;
                LecturesDifficulties::removeLectureDifficulties($model->id);
                foreach ($post['difficulties'] as $pid => $value) {
                    $difficulty = new LecturesDifficulties();
                    $difficulty->diff_id = $pid;
                    $difficulty->lecture_id = $model->id;
                    $difficulty->value = $value ?? 0;
                    if (is_numeric($difficulty->value)) {
                        $sum += $difficulty->value;
                    }
                    $difficulty->save();
                }
                $model->complexity = (int) $sum;
                $model->updated = date('Y-m-d H:i:s', time());
                $model->save(false);
            }
            if (isset($post['handdifficulties'])) {
                Lectureshanddifficulties::removeLectureDifficulties($id);
                foreach ($post['handdifficulties'] as $pid => $value) {
                    $handdifficulty = new Lectureshanddifficulties();
                    $handdifficulty->category_id = $pid;
                    $handdifficulty->lecture_id = $model->id;
                    $handdifficulty->save();
                }
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
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        return $this->render('update', [
            'model' => $model,
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties,
            'evaluations' => $evaluations,
            'lectureDifficulties' => $lectureDifficulties,
            'lectureHandDifficulties' => $lectureHandDifficulties,
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
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
