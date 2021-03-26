<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\PlanFiles;
use app\models\SentInvoices;
use app\models\StudentSubplanPauses;
use app\models\SchoolSubPlans;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

class StudentSubPlansController extends Controller
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
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        $subplan = StudentSubPlans::getCurrentForStudent($id);

        if (!$subplan) {
            return $this->render('view', [
                'subplan' => null,
                'planFiles' => null,
                'planPauses' => null,
                'newPause' => null,
                'remainingPauseWeeks' => null,
                'planCurrentlyPaused' => null,
            ]);
        }

        $planFiles = PlanFiles::getFilesForPlan($subplan["plan_id"])->asArray()->all();
        $planPauses = null;
        if (StudentSubplanPauses::studentHasAnyPauses($id)) {
            $planPauses = new ActiveDataProvider([
                'query' => StudentSubplanPauses::getForStudentSubplan($subplan['id']),
            ]);
        }
        $newPause = new StudentSubplanPauses;
        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks($id);
        $planCurrentlyPaused = StudentSubPlans::isPlanCurrentlyPaused($id);

        return $this->render('view', [
            'subplan' => $subplan,
            'planFiles' => $planFiles,
            'planPauses' => $planPauses,
            'newPause' => $newPause,
            'remainingPauseWeeks' => $remainingPauseWeeks,
            'planCurrentlyPaused' => $planCurrentlyPaused,
        ]);
    }

    public function actionDelete($userId)
    {

        StudentSubplans::resetActivePlanForUser($userId);

        return $this->redirect(Yii::$app->request->referrer);
    }
}
