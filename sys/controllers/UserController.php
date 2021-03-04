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
use app\models\SchoolSubPlans;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\Payer;
use app\models\SchoolStudent;
use app\models\StudentSubPlans;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\ArrayHelper;

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
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            // return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                            return true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'open-chat' => ['post'],
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
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $get = Yii::$app->request->queryParams;
        $lectures = Lectures::getLectures();
        $isCurrentUserTeacher = Users::isCurrentUserTeacher();
        $searchModel = $isCurrentUserTeacher ? new TeacherUserSearch() : new UserSearch();
        $dataProvider = $searchModel->search($get);
        $schoolSubPlanPrices = SchoolSubPlans::getPrices();
        $planEndDates = StudentSubPlans::getReadablePlanEndDates();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'get' => $get,
            'lectures' => $lectures,
            'schoolSubPlanPrices' => $schoolSubPlanPrices,
            'planEndDates' => $planEndDates,
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
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $studentSubPlan = StudentSubPlans::getCurrentForStudent($id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'studentSubPlan' => $studentSubPlan
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
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new Users();
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $post = Yii::$app->request->post();
        $currentUserEmail = Yii::$app->user->identity->email;
        $currentUser = Users::getByEmail($currentUserEmail);
        $isCurrentUserTeacher = Users::isTeacher($currentUserEmail);
        $studentSubplan = new StudentSubPlans;

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
            $model->allowed_to_download_files = false;
            if (isset($post['Users']['allowed_to_download_files']) && $post['Users']['allowed_to_download_files']) {
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
            'studentSubplan' => $studentSubplan,
        ]);
    }

    public function actionUpdate($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $studentGoals = Studentgoals::getUserGoals($id);
        $studentHandGoals = Studenthandgoals::getUserGoals($id);
        $difficulties = Difficulties::getDifficulties();
        $handdifficulties = Handdifficulties::getDifficulties();
        $schoolSubPlans = SchoolSubPlans::getMappedForSelection();
        $studentSubplan = StudentSubPlans::getCurrentForStudent($model->id);
        if(!$studentSubplan) $studentSubplan = new StudentSubPlans; 

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
            $model->allowed_to_download_files = false;
            if (isset($post['Users']['allowed_to_download_files'])) {
                $model->allowed_to_download_files = $post['Users']['allowed_to_download_files'];
            }
            if (isset($post['Users']['about'])) {
                $model->about = $post['Users']['about'];
            }
           
            if (isset($post['StudentSubPlans'])) {
                $postData = $post['StudentSubPlans'];              
              
                if ($studentSubplan) {
                    $schoolSubplanChanged = $studentSubplan['plan_id'] !== (int) $postData['plan_id'];

                    if($schoolSubplanChanged){
                        StudentSubPlans::resetActivePlanForUser($model->id);

                        $studentSubplan = new StudentSubPlans;
                        $studentSubplan->user_id = $model->id;
                        $studentSubplan->plan_id = $postData["plan_id"];
                        $studentSubplan->is_active = true;
                        $studentSubplan->start_date = $postData["start_date"];
                        $studentSubplan->sent_invoices_count = $postData["sent_invoices_count"] ? $postData["sent_invoices_count"] : 0;
                        $studentSubplan->times_paid = $postData["times_paid"] ? $postData["times_paid"] : 0;
                        $studentSubplan->save();
                    }else{
                        $studentSubplan->plan_id = $postData["plan_id"];
                        $studentSubplan->start_date = $postData["start_date"];
                        $studentSubplan->sent_invoices_count = $postData["sent_invoices_count"] ? $postData["sent_invoices_count"] : 0;
                        $studentSubplan->times_paid = $postData["times_paid"] ? $postData["times_paid"] : 0;
                        $studentSubplan->update();
                    }                    
                } else {
                    StudentSubPlans::resetActivePlanForUser($model->id);

                    $studentSubplan = new StudentSubPlans;
                    $studentSubplan->user_id = $model->id;
                    $studentSubplan->plan_id = $postData["plan_id"];
                    $studentSubplan->is_active = true;
                    $studentSubplan->start_date = $postData["start_date"];
                    $studentSubplan->times_paid = $postData["times_paid"];
                    $studentSubplan->save();
                }
            }
            if (isset($post['Users']['payer']) && $post['Users']['payer']) {
                $postData = $post['Users']['payer'];

                $payer = Payer::getForStudent($model->id);
                $newPayer = false;
                if(!$payer){
                    $payer = new Payer;
                    $newPayer = true;
                }

                $payer->name = $postData["name"];
                $payer->address = $postData["address"];
                $payer->personal_code = $postData["personal_code"];
                $payer->registration_number = $postData["registration_number"];
                $payer->pvn_registration_number = $postData["pvn_registration_number"];
                $payer->bank = $postData["bank"];
                $payer->swift = $postData["swift"];
                $payer->account_number = $postData["swift"];

                if($newPayer){
                    $payer->save();
                } else {
                    $payer->update();
                }

                Yii::$app->session->setFlash('success', 'Maksātāja informācija saglabāta!');
            }

            $model->update();

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'studentGoals' => $studentGoals,
                'studentHandGoals' => $studentHandGoals,
                'difficulties' => $difficulties,
                'handdifficulties' => $handdifficulties,
                'schoolSubPlans' => $schoolSubPlans,
                'studentSubplan' => $studentSubplan,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        Yii::$app
            ->db
            ->createCommand()
            ->delete('userlectureevaluations', ['user_id' => $id])
            ->execute();

        Yii::$app
            ->db
            ->createCommand()
            ->delete('studentgoals', ['user_id' => $id])
            ->execute();

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionOpenChat(){
        $model = $this->findModel(Yii::$app->user->identity->id);
        date_default_timezone_set('EET');   
        $time = time();
        $currentDateTime = date("Y-m-d H:i:s", $time);
        $model->last_opened_chat = $currentDateTime;
        $saved = $model->save();
        return $saved;
    }

    public function actionRequestMoreTasks($id){
        $student = self::findModel($id);
        $student->wants_more_lessons = true;
        $student->update();

        Yii::$app->session->setFlash('success', 'Paldies par ziņu! Nākamajā reizē sūtīsim vairāk uzdevumus! Lai mierīga diena!');
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = Users::find()->where(['users.id' => $id])->joinWith("payer")->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
