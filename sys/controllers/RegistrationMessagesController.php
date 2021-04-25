<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\RegistrationMessage;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class RegistrationMessagesController extends Controller
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionCreate($withInstrument, $withExperience)
    {
        $model = new RegistrationMessage;
        $model->school_id = School::getCurrentSchoolId();
        $model->for_students_with_instrument = $withInstrument;
        $model->for_students_with_experience = $withExperience;
        $valid = $model->load(Yii::$app->request->post()) && $model->validate();

        if ($valid && $model->save()) {
            return $this->redirect(Url::to(['registration-lessons/index']));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $valid = $model->load(Yii::$app->request->post()) && $model->validate();

        if ($valid && $model->update()) {
            return $this->redirect(Url::to(['registration-lessons/index']));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {

        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = RegistrationMessage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
