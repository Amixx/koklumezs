<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\RentForm;
use app\models\RegistrationMessage;
use app\models\SignupQuestions;
use app\models\SignUpForm;
use app\models\SchoolStudent;
use app\models\School;
use app\models\RegistrationLesson;
use app\helpers\EmailSender;
use app\helpers\GuestLayoutHelper;
use app\helpers\InvoiceManager;
use app\models\Chat;
use app\models\RegistrationQuestionForm;
use app\models\SchoolTeacher;

class RegistrationController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex($s, $l)
    {
        $this->layout = '@app/views/layouts/signup';
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        if (Yii::$app->session['signupModel']) {
            Yii::$app->session['signupModel'] = null;
        }

        Yii::$app->language = $l;

        $school = School::findOne($s);
        $layoutHelper = new GuestLayoutHelper($school);
        $this->view->params['layoutHelper'] = $layoutHelper;

        $model = new SignUpForm;
        $post = Yii::$app->request->post();

        if ($post) {
            $model->load($post);
            Yii::$app->session['signupModel'] = $model;
            return $this->redirect(['question', 's' => $s, 'l' => $l]);
        }

        return $this->render('index', [
            'model' => $model,
            'registration_title' => $school['registration_title'],
            'registration_image' => $school['registration_image'],
        ]);
    }


    public function actionQuestion($s, $l, $i = 0)
    {
        $school = School::findOne($s);
        $layoutHelper = new GuestLayoutHelper($school);

        $this->layout = '@app/views/layouts/login';
        $this->view->params['layoutHelper'] = $layoutHelper;
        Yii::$app->language = $l;

        $questionOfIndex = SignupQuestions::getQuestionOfIndexForSchool($school, $i);

        if (!$questionOfIndex) {
            return $this->redirect(['profile-creation', 's' => $s, 'l' => $l]);
        }

        $model = new RegistrationQuestionForm;

        $post = Yii::$app->request->post();
        if ($post && $model->load($post) && $model->validate()) {
            $qna = Yii::$app->session['questionsAndAnswers'];
            $qna[] = [
                'question' => $questionOfIndex['text'],
                'answer' => $model->answer,
            ];
            Yii::$app->session['questionsAndAnswers'] = $qna;

            if (SignupQuestions::isInstrumentQuestion($questionOfIndex['id'])) {
                $hasInstrument = SignupQuestions::isAnswerPositive($model->answer);
                $signupModel = Yii::$app->session['signupModel'];

                if (!$signupModel) return $this->redirect(['index', 's' => $s, 'l' => $l]);

                $signupModel->ownsInstrument = $hasInstrument;
                Yii::$app->session['signupModel'] = $signupModel;


                if (!$hasInstrument) {
                    return $this->redirect(['profile-creation', 's' => $s, 'l' => $l]);
                }
            }

            if (SignupQuestions::isExperienceQuestion($questionOfIndex['id'])) {
                $hasExperience = SignupQuestions::isAnswerPositive($model->answer);
                $signupModel = Yii::$app->session['signupModel'];

                if (!$signupModel) return $this->redirect(['index', 's' => $s, 'l' => $l]);

                $signupModel->hasExperience = $hasExperience;
                Yii::$app->session['signupModel'] = $signupModel;
            }

            return $this->redirect(['question', 's' => $s, 'l' => $l, 'i' => $i + 1]);
        }

        return $this->render('question', [
            'model' => $model,
            'question' => $questionOfIndex,
        ]);
    }

    public function actionProfileCreation($s, $l)
    {
        $this->layout = '@app/views/layouts/signup';

        Yii::$app->language = $l;

        $school = School::findOne($s);
        $schoolTeacher = SchoolTeacher::getBySchoolId($s)["user"];
        $layoutHelper = new GuestLayoutHelper($school);
        $this->view->params['layoutHelper'] = $layoutHelper;

        $model = SignUpForm::fromSession();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model['language'] = $l;

            if (!$model->ownsInstrument) {
                Yii::$app->session['signupModel'] = $model;
                return $this->redirect(Url::to(["registration/rent", 's' => $s, 'l' => $l]));
            }

            $user = $model->signUp();
            if ($user && SchoolStudent::createNew($s, $user->id, false, $model->ownsInstrument)) {
                Yii::$app->user->login($user);
                $user->updateLoginTime();

                RegistrationLesson::assignToStudent($s, $user->id, $model);
                EmailSender::sendNewStudentNotification($user, $school['email']);

                $chatMessage = RegistrationMessage::getBody($s, $model->ownsInstrument, $model->hasExperience);
                if ($chatMessage) {
                    Chat::addNewMessage($chatMessage, $schoolTeacher['id'], $user['id']);
                }

                if ($school['registration_message'] != null && $model->ownsInstrument) {
                    EmailSender::sendPostSignupMessage($school['registration_message'], $school['email'], $user['email']);
                }

                Yii::$app->session->set("renderPostRegistrationModal", true);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Hey! You\'ve registered successfully. Your 2 week trial period will start after you play and evaluate currently assigned lessons. After that, we will see that you are ready to learn') . '!');
                return $this->redirect(['lekcijas/index']);
            }
        }

        $model->password = '';
        $model->passwordRepeat = '';
        $instrument = strtolower($school['instrument']);
        return $this->render('profile-creation', [
            'model' => $model,
            'registration_title' => $school['registration_title'],
            'registration_image' => $school['registration_image'],
            'instrument' => $instrument,
        ]);
    }

    public function actionRent($s, $l)
    {
        $school = School::findOne($s);
        $schoolTeacher = SchoolTeacher::getBySchoolId($s)["user"];
        $layoutHelper = new GuestLayoutHelper($school);

        $this->layout = '@app/views/layouts/login';
        $this->view->params['layoutHelper'] = $layoutHelper;
        Yii::$app->language = $l;

        $signupModel = Yii::$app->session['signupModel'];

        if (!$signupModel) return $this->redirect(['index', 's' => $s, 'l' => $l]);

        $model = new RentForm;
        $urlToContract = $school->rent_contract;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = RentForm::registerUser($signupModel);

            if ($user && SchoolStudent::createNew($s, $user->id, true, false)) {
                RegistrationLesson::assignToStudent($s, $user->id, $signupModel);
                $user->updateLoginTime();

                $chatMessage = RegistrationMessage::getBody($s, $signupModel->ownsInstrument, $signupModel->hasExperience);
                if ($chatMessage) {
                    Chat::addNewMessage($chatMessage, $schoolTeacher['id'], $user['id']);
                }

                if ($school['renter_message'] != null && $school['rent_schoolsubplan_id'] != null) {
                    $studentSubplan = RentForm::registerPlanForUser($user->id, $school['rent_schoolsubplan_id']);
                    InvoiceManager::sendAdvanceInvoice($user, $studentSubplan, true);
                }

                $sent = EmailSender::sendRentNotification($user, $school['email']);

                if ($sent) {
                    Yii::$app->session->setFlash('success', \Yii::t(
                        'app',
                        'Hey! Your profile has been successfully created! We sent you information about renting the instrument to your e-mail. While it\'s still on the way, you can watch the introductory video on the platform.'
                    ));
                    Yii::$app->user->login($user, 3600 * 24 * 30);
                    return $this->redirect(['lekcijas/index']);
                }
            }
        }

        return $this->render('rent', [
            'text' => $school['rent_text'],
            'model' => $model,
            'backUrl' => Url::to(['registration/index', 's' => $s, 'l' => $l]),
            'urlToContract' => $urlToContract,
        ]);
    }

    public function actionReceivedInstrument()
    {
        $schoolStudent = SchoolStudent::getSchoolStudent(Yii::$app->user->identity->id);
        $schoolStudent['has_instrument'] = true;
        $schoolStudent->save();

        Yii::$app->session->set("renderPostRegistrationModal", true);

        return $this->redirect(Yii::$app->request->referrer);
    }
}
