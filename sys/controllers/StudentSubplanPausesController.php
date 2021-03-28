<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\StudentSubplanPauses;
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

    public function actionTeacherCreate()
    {
        $model = new StudentSubplanPauses();
        $users = Users::getStudentNamesForSchool();

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST['user_id'])) {
                $userId = $_POST['user_id'];
            }
            $studentSubplan = StudentSubPlans::getCurrentForStudent($userId);
            if ($studentSubplan) {
                $model->studentsubplan_id = $studentSubplan['id'];
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Plāna pauze izveidota!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Izvēlētajam skolēnam nav piešķirts plāns!');
            }

            return $this->redirect(Url::to(['school-sub-plans/index']));
        }

        return $this->render('create', [
            'model' => $model,
            'users' => $users,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $users = Users::getStudentNamesForSchool();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Plāna pauzes labojumi saglabāti!');
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
            Yii::$app->session->setFlash('success', 'Plāna pauze dzēsta!');
        }

        return $this->redirect(Url::to(['school-sub-plans/index']));
    }

    public function actionCreate()
    {
        $model = new StudentSubplanPauses();

        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks(Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model["weeks"] > $remainingPauseWeeks) {
                Yii::$app->session->setFlash('error', 'Neizdevās nosūtīt e-pasta adresi, lai atjaunotu paroli.');
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
