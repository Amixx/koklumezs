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

        return $this->render('for-user', [
            'dataProvider' => $studentSubplansADP,
        ]);
    }

    public function actionView($id)
    {
        $subplan = StudentSubPlans::findOne($id);

        $planFiles = PlanFiles::getFilesForPlan($subplan["plan_id"])->asArray()->all();
        $planPauses = new ActiveDataProvider([
            'query' => StudentSubplanPauses::getForStudentSubplan($subplan['id']),
        ]);
        $newPause = new StudentSubplanPauses;
        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks($subplan['id']);
        $planCurrentlyPaused = StudentSubPlans::isPlanCurrentlyPaused($subplan['id']);

        return $this->render('view', [
            'subplan' => $subplan,
            'planFiles' => $planFiles,
            'planPauses' => $planPauses,
            'newPause' => $newPause,
            'remainingPauseWeeks' => $remainingPauseWeeks,
            'planCurrentlyPaused' => $planCurrentlyPaused,
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
}
