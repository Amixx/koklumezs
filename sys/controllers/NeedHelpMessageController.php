<?php

namespace app\controllers;

use app\models\Lectures;
use app\models\NeedHelpMessages;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NeedHelpMessageController extends Controller
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
                            return !empty(Yii::$app->user->identity);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }


    public function actionCreate()
    {
        $post = $this->request->post();

        $model = new NeedHelpMessages;
        $model->author_id = Yii::$app->user->identity->id;
        $model->lesson_id = $post['lessonId'];
        $model->message = $post['message'];

        if ($model->save()) {
            return json_encode($model);
        }
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
        if (($model = NeedHelpMessages::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
