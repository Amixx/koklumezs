<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\RegistrationLesson;
use app\models\School;
use app\models\Lectures;
use app\models\RegistrationMessage;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class RegistrationLessonsController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $schoolId = School::getCurrentSchoolId();
        $lectures = Lectures::getLectures();
        $conf = [];

        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $wi = (bool)$i; // for students with instrument
                $we = (bool)$j; // for students with experience

                $conf[$i][$j] = [
                    'lessons' => new ActiveDataProvider([
                        'query' => RegistrationLesson::getLessonsForIndex($schoolId, $wi, $we),
                    ]),
                    'message' => RegistrationMessage::getForIndex($schoolId, $wi, $we),
                    'title' => self::getItemTitle($wi, $we),
                    'wi' => $wi,
                    'we' => $we,
                ];
            }
        }

        $model = new RegistrationLesson;
        $model->school_id = $schoolId;

        $post = Yii::$app->request->post();
        if ($post) {
            $model->lesson_id = (int)$post['RegistrationLesson']['lesson_id'];
            $model->for_students_with_instrument = (bool)$post['RegistrationLesson']['for_students_with_instrument'];
            $model->for_students_with_experience = (bool)$post['RegistrationLesson']['for_students_with_experience'];

            if ($model->save()) {
                $model = new RegistrationLesson;
            }
        }

        return $this->render('index', [
            'conf' => $conf,
            'lectures' => $lectures,
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = RegistrationLesson::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private static function getItemTitle($wi, $we)
    {
        $a = $wi ? "With" : "Without";
        $b = $we ? "with" : "without";

        return "$a instrument; $b experience";
    }
}
