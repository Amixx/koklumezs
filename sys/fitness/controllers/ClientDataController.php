<?php

namespace app\fitness\controllers;

use app\fitness\models\ClientData;
use app\models\Users;
use http\Client;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ClientDataController extends Controller
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
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionUpdate($userId)
    {
        $user = Users::findOne(['id' => $userId]);
        $clientData = ClientData::findOne(['user_id' => $userId]);
        if (!$clientData) {
            $clientData = new ClientData();
            $clientData->user_id = $userId;
        }

        $post = Yii::$app->request->post();
        if ($post && $clientData->load($post) && $clientData->validate()) {
            $clientData->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Client data updated!'));
        }

        return $this->render('@app/fitness/views/client-data/update', [
            'model' => $clientData,
            'user' => $user,
        ]);
    }
}