<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\RegistrationLesson;
use app\models\School;
use app\models\Lectures;
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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $schoolId = School::getCurrentSchoolId();
        $lectures = Lectures::getLectures();

        $registrationLessons = [];

        $registrationLessons['without_instrument']['without_experience'] = new ActiveDataProvider([
            'query' => RegistrationLesson::getLessonsForIndex($schoolId, false, false),
        ]);
        $registrationLessons['without_instrument']['with_experience'] = new ActiveDataProvider([
            'query' => RegistrationLesson::getLessonsForIndex($schoolId, false, true),
        ]);
        $registrationLessons['with_instrument']['without_experience'] = new ActiveDataProvider([
            'query' => RegistrationLesson::getLessonsForIndex($schoolId, true, false),
        ]);
        $registrationLessons['with_instrument']['with_experience'] = new ActiveDataProvider([
            'query' => RegistrationLesson::getLessonsForIndex($schoolId, true, true),
        ]);


        $model = new RegistrationLesson;
        $model->school_id = $schoolId;

        $post = Yii::$app->request->post();
        if($post){
            $model->lesson_id = (int)$post['RegistrationLesson']['lesson_id'];
            $model->for_students_with_instrument = (bool)$post['RegistrationLesson']['for_students_with_instrument'];
            $model->for_students_with_experience = (bool)$post['RegistrationLesson']['for_students_with_experience'];

            if($model->save()){
                $model = new RegistrationLesson;
            }
        }

        return $this->render('index', [
            'registrationLessons' => $registrationLessons,
            'lectures' => $lectures,
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        
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
}
