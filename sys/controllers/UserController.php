<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Lectures;
use app\models\UserSearch;
use app\models\TeacherUserSearch;
use app\models\Studentgoals;
use app\models\Difficulties;
use app\models\SchoolSubPlans;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\Payer;
use app\models\StartLaterCommitments;
use app\models\SchoolStudent;
use app\models\StudentSubPlans;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends Controller
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
                            return true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
        $studentSubPlans = StudentSubPlans::getActivePlansForStudentADP($id);
        $subPlans = $studentSubPlans->query->all();
        $planEndDates = [];
        foreach ($subPlans as $subPlan) {
            $planId = $subPlan->id;
            $plan = StudentSubPlans::getSubPlanById($planId);
            $planEndDate = StudentSubPlans::getPlanEndDateString($plan);
            array_push($planEndDates, ['planId' => $planId, 'endDate' => $planEndDate]);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'studentSubPlans' => $studentSubPlans,
            'planEndDates' => $planEndDates
        ]);
    }

    public function actionCreate()
    {
        $model = new Users();
        $difficulties = Difficulties::getDifficulties();
        $post = Yii::$app->request->post();
        $currentUserEmail = Yii::$app->user->identity->email;
        $currentUser = Users::getByEmail($currentUserEmail);
        $isCurrentUserTeacher = Users::isTeacher($currentUserEmail);
        $studentSubplan = new StudentSubPlans;

        if ($model->load($post)) {
            $isNewUserTeacher = isset($post['Users']['user_level']) && $post['Users']['user_level'] == 'Teacher';
            if ($isNewUserTeacher && !$post['teacher_instrument']) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Select teacher instrument') . '!');
                return $this->redirect(['create']);
            }
            if ($isCurrentUserTeacher) {
                $model->user_level = Users::ROLE_USER;
            }
            $model->password = \Yii::$app->security->generatePasswordHash($model->password);
            $model->created_at = date('Y-m-d H:i:s', time());
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
                $newSchoolStudent->save();
            }
            if ($created) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'User created successfully') . '!');
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'User not created') . '!');
                return $this->redirect(['index']);
            }

            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            'studentGoals' => [],
            'difficulties' => $difficulties,
            'studentSubplan' => $studentSubplan,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $studentGoals = Studentgoals::getUserGoals($id);
        $difficulties = Difficulties::getDifficulties();
        $schoolSubPlans = SchoolSubPlans::getMappedForSelection();
        $studentSubplans = StudentSubPlans::getActivePlansForStudentADP($model->id);

        $studentSubplanModel = new StudentSubPlans;
        $studentSubplanModel->user_id = $model->id;
        $studentSubplanModel->start_date = date('Y-m-d H:i:s', time());
        $studentSubplanModel->sent_invoices_count = 0;
        $studentSubplanModel->times_paid = 0;
        $studentSubplanModel->is_active = true;

        if ($model->load($post)) {
            if (!empty($post['Users']['password'])) {
                $model->password = \Yii::$app->security->generatePasswordHash($post['Users']['password']);
            } else {
                unset($model->password);
            }
            if (isset($post['studentgoals'])) {
                Studentgoals::removeUserGoals($id);
                if (isset($post['studentgoals']['now'])) {
                    Studentgoals::addCurrentGoals($post['studentgoals']['now'], $model->id);
                }
                if (isset($post['studentgoals']['future'])) {
                    Studentgoals::addFutureGoals($post['studentgoals']['future'], $model->id);
                }
            }
            $model->allowed_to_download_files = false;
            if (isset($post['Users']['allowed_to_download_files'])) {
                $model->allowed_to_download_files = $post['Users']['allowed_to_download_files'];
            }
            if (isset($post['Users']['about'])) {
                $model->about = $post['Users']['about'];
            }

            if (isset($post['StudentSubPlans']) && $post['StudentSubPlans']['plan_id']) {
                $studentSubplanModel->load($post);
                if ($studentSubplanModel->validate()) {
                    $studentSubplanModel->save();
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Couldn\'t save plan - incorrect data given') . '!');
                }
            }
            if (isset($post['Users']['payer']) && $post['Users']['payer']) {
                $postData = $post['Users']['payer'];

                $payer = Payer::getForStudent($model->id);
                $newPayer = false;
                if (!$payer) {
                    $payer = new Payer;
                    $payer->user_id = $model->id;
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

                if ($payer->validate()) {
                    if ($newPayer) {
                        $payer->save();
                    } else {
                        $payer->update();
                    }

                    Yii::$app->session->setFlash('success', Yii::t('app', 'The payer\'s information saved') . '!');
                }
            }

            $model->update();

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'studentGoals' => $studentGoals,
                'difficulties' => $difficulties,
                'schoolSubPlans' => $schoolSubPlans,
                'studentSubplans' => $studentSubplans,
                'studentSubplanModel' => $studentSubplanModel,
            ]);
        }
    }

    public function actionDelete($id)
    {
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

    public function actionRequestMoreTasks($id)
    {
        $student = self::findModel($id);
        $student->wants_more_lessons = true;
        $student->update();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Thank you for your message! Next time we will send more tasks! Have a good day!'));
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionStartNow()
    {
        $schoolStudent = SchoolStudent::findOne(['user_id' => Yii::$app->user->identity->id]);
        $schoolStudent->show_real_lessons = true;
        $schoolStudent->update();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionStartLater()
    {
        $post = Yii::$app->request->post();
        $model = new StartLaterCommitments();
        $model->load($post);
        $model->user_id = Yii::$app->user->identity->id;

        $model->validate() && $model->save();

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
