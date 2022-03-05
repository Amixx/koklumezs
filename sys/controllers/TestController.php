<?php

namespace app\controllers;

use Exception;
use yii\web\Controller;
use yii\filters\VerbFilter;

class TestController extends Controller
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

    public function actionThrowError()
    {
        throw new Exception("Testa errors!");
    }
    
}
