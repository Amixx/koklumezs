<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\PlanFiles;
use app\models\StudentSubplanPauses;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class StudentSubPlansController extends Controller
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
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionForUser($studentId)
    {
        $studentSubplansADP = StudentSubPlans::getActivePlansForStudentADP($studentId);
        $subPlans = $studentSubplansADP->query->all();
        $planEndDates = [];
        foreach ($subPlans as $subPlan) {
            $planId = $subPlan->id;
            $plan = StudentSubPlans::getSubPlanById($planId);
            $planEndDate = StudentSubPlans::getPlanEndDateString($plan);
            array_push($planEndDates, ['planId' => $planId, 'endDate' => $planEndDate]);
        }

        return $this->render('for-user', [
            'dataProvider' => $studentSubplansADP,
            'planEndDates' => $planEndDates
        ]);
    }

    public function actionView($id)
    {
        $subplan = StudentSubPlans::findOne($id);
        $planFiles = PlanFiles::getFilesForPlan($subplan["plan_id"])->asArray()->all();


        return $this->render('view', [
            'subplan' => $subplan,
            'planFiles' => $planFiles,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = StudentSubPlans::findOne($id);
        $post = Yii::$app->request->post();

        if ($post && $model->load($post) && $model->validate()) {
            $saved = $model->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Changes saved') . '!');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        StudentSubplans::setStudentSubplanInactive($id);

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPause($id)
    {
        $planPauses = new ActiveDataProvider([
            'query' => StudentSubplanPauses::getForStudentSubplan($id),
        ]);
        $newPause = new StudentSubplanPauses;
        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks($id);
        $planCurrentlyPaused = StudentSubPlans::isPlanPaused($id);


        return $this->render('pause', [
            'subplanId' => $id,
            'planPauses' => $planPauses,
            'newPause' => $newPause,
            'remainingPauseWeeks' => $remainingPauseWeeks,
            'planCurrentlyPaused' => $planCurrentlyPaused,
        ]);
    }
}
