<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\StudentSubplanPauses;
use app\models\TeacherCreatePauseForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

class StudentSubplanPausesController extends Controller
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
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StudentSubplanPauses::getForCurrentSchool(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPickStudent()
    {
        $students = Users::getStudentNamesForSchool();
        return $this->render('pick-student', [
            'students' => $students,
        ]);
    }

    public function actionTeacherCreate($userId)
    {
        if (!$userId) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $formModel = new TeacherCreatePauseForm();
        $post = Yii::$app->request->post();

        if ($post && $formModel->load($post) && $formModel->validate()) {
            $studentSubplan = StudentSubPlans::findOne($formModel['plan_id']);
            if ($studentSubplan) {

                $model = StudentSubplanPauses::createFromTeacherForm($formModel, $userId);

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Plan pause created') . '!');
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'The student does not have assigned subscription plan') . '!');
            }

            return $this->redirect(Url::to(['school-sub-plans/index']));
        }

        $studentSubPlans = StudentSubPlans::getForStudentMapped($userId);

        return $this->render('create', [
            'model' => $formModel,
            'studentSubPlans' => $studentSubPlans,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $users = Users::getStudentNamesForSchool();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Plan pause changes saved') . '!');
            return $this->redirect(Url::to(['school-sub-plans/index']));
        }

        return $this->render('update', [
            'model' => $model,
            'users' => $users,
            'userId' => $id,
        ]);
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Plan pause deleted') . '!');
        }

        return $this->redirect(Url::to(['school-sub-plans/index']));
    }

    public function actionCreate($id)
    {
        $model = new StudentSubplanPauses();

        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model["weeks"] > $remainingPauseWeeks) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Allowed weeks exceeded') . '!');
            } else {
                $model->save();
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = StudentSubplanPauses::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
