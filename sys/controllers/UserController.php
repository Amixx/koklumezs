<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Lectures;
use app\models\UserSearch;
use app\models\TeacherUserSearch;
use app\models\Studentgoals;
use app\models\Difficulties;
use app\models\Handdifficulties;
use app\models\Studenthandgoals;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\SchoolStudent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UserController extends Controller
{
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->username);
                        }
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
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
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $get = Yii::$app->request->queryParams;
        $lectures = Lectures::getLectures();
        $isCurrentUserTeacher = Users::isCurrentUserTeacher();
        $searchModel = $isCurrentUserTeacher ? new TeacherUserSearch() : new UserSearch();
        $dataProvider = $searchModel->search($get);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get,
            'lectures' => $lectures
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
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
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
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
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new Users();
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $post = Yii::$app->request->post();
        $currentUserUsername = Yii::$app->user->identity->username;
        $currentUser = Users::getByUsername($currentUserUsername);
        $isCurrentUserTeacher = Users::isTeacher($currentUserUsername);

        if ($model->load($post)) {
            $isNewUserTeacher = isset($post['Users']['user_level']) && $post['Users']['user_level'] == 'Teacher';
            if ($isNewUserTeacher and !$post['teacher_instrument']) {
                Yii::$app->session->setFlash('error', "Norādiet skolotāja instrumentu!");
                return $this->redirect(['create']);
            }
            if ($isCurrentUserTeacher) {
                $model->user_level = Users::ROLE_USER;
            }
            $model->password = \Yii::$app->security->generatePasswordHash($model->password);
            $model->created_at = date('Y-m-d H:i:s', time());
            $model->dont_bother = $post['Users']['dont_bother'] ? $post['Users']['dont_bother'] . ' 23:59:59' : $model->dont_bother;
            if (isset($post['Users']['allowed_to_download_files'])) {
                $model->allowed_to_download_files = $post['Users']['allowed_to_download_files'];
            }
            if (isset($post['Users']['about'])) {
                $model->about = $post['Users']['about'];
            }
            $created = $model->save();
            if ($isNewUserTeacher) {
                $newSchool = new School;
                $newSchool->instrument = $post['teacher_instrument'];
                $newSchool->save();

                $newSchoolTeacher = new SchoolTeacher;
                $newSchoolTeacher->school_id = $newSchool->id;
                $newSchoolTeacher->user_id = $model->id;
                $newSchoolTeacher->instrument = $post['teacher_instrument'];
                $newSchoolTeacher->save();
            }
            if ($isCurrentUserTeacher) {
                $teacher = SchoolTeacher::getSchoolTeacher($currentUser->id);
                $newSchoolStudent = new SchoolStudent;
                $newSchoolStudent->school_id = $teacher->school_id;
                $newSchoolStudent->user_id = $model->id;
                $studentAddedToSchool = $newSchoolStudent->save();
            }
            if ($created) {
                Yii::$app->session->setFlash('success', "User created successfully!");
            } else {
                Yii::$app->session->setFlash('error', "User not created!");
                return $this->redirect(['index']);
            }

            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            'studentGoals' => [],
            'studentHandGoals' => [],
            'difficulties' => $difficulties,
            'handdifficulties' => $handdifficulties,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
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
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $studentGoals = Studentgoals::getUserGoals($id);
        $studentHandGoals = Studenthandgoals::getUserGoals($id);
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();

        if ($model->load($post)) {
            if (!empty($post['Users']['password'])) {
                $model->password = \Yii::$app->security->generatePasswordHash($post['Users']['password']);
            } else {
                unset($model->password);
            }
            if (isset($post['studentgoals'])) {
                Studentgoals::removeUserGoals($id);
                if (isset($post['studentgoals']['now'])) {
                    foreach ($post['studentgoals']['now'] as $pid => $value) {
                        $goal = new Studentgoals();
                        $goal->user_id = $model->id;
                        $goal->diff_id = $pid;
                        $goal->type = Studentgoals::NOW;
                        $goal->value = $value ?? 0;
                        $goal->save();
                    }
                }
                if (isset($post['studentgoals']['future'])) {
                    foreach ($post['studentgoals']['future'] as $pid => $value) {
                        $goal = new Studentgoals();
                        $goal->user_id = $model->id;
                        $goal->diff_id = $pid;
                        $goal->type = Studentgoals::FUTURE;
                        $goal->value = $value ?? 0;
                        $goal->save();
                    }
                }
            }
            if (isset($post['studenthandgoals'])) {
                Studenthandgoals::removeUserGoals($id);
                foreach ($post['studenthandgoals'] as $pid => $value) {
                    $goal = new Studenthandgoals();
                    $goal->user_id = $model->id;
                    $goal->category_id = $pid;
                    $goal->save();
                }
            }
            $model->dont_bother = $post['Users']['dont_bother'] ? $post['Users']['dont_bother'] . ' 23:59:59' : $model->dont_bother;
            if (isset($post['Users']['allowed_to_download_files'])) {
                $model->allowed_to_download_files = $post['Users']['allowed_to_download_files'];
            }
            if (isset($post['Users']['about'])) {
                $model->about = $post['Users']['about'];
            }

            $model->update();
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'studentGoals' => $studentGoals,
                'studentHandGoals' => $studentHandGoals,
                'difficulties' => $difficulties,
                'handdifficulties' => $handdifficulties
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
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
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
