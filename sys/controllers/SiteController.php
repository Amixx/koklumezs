<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RentForm;
use app\models\RegistrationMessage;
use app\models\SignupQuestions;
use app\models\SignUpForm;
use app\models\SchoolStudent;
use app\models\School;
use app\models\RegistrationLesson;
use app\models\Users;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\ResendVerificationEmailForm;
use app\models\VerifyEmailForm;
use app\helpers\EmailSender;
use app\helpers\GuestLayoutHelper;
use app\helpers\InvoiceManager;
use app\models\Chat;
use app\models\SchoolTeacher;
use yii\web\BadRequestHttpException;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $email = isset(Yii::$app->user->identity->email) ? Yii::$app->user->identity->email : null;

        if (Users::isAdminOrTeacher($email)) {
            return $this->redirect(['/lectures']);
        } elseif (Users::isStudent($email)) {
            return $this->redirect(['/lekcijas']);
        } else {
            return $this->redirect(['/site/login']);
        }
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = '@app/views/layouts/login';
        $layoutHelper = new GuestLayoutHelper(null);
        $this->view->params['layoutHelper'] = $layoutHelper;

        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Pārbaudiet savu e-pastu, lai turpinātu paroles atjaunošanas procesu.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Neizdevās nosūtīt e-pastu, lai atjaunotu paroli.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $this->layout = '@app/views/layouts/login';
        $layoutHelper = new GuestLayoutHelper(null);
        $this->view->params['layoutHelper'] = $layoutHelper;

        try {
            $model = new ResetPasswordForm($token);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Jaunā parole saglabāta.');
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($user = $model->verifyEmail() && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }
        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionLogin($s = null, $l = null)
    {
        $this->layout = '@app/views/layouts/login';

        Yii::$app->language = $l;
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $loginTitle = '';
        $school = null;
        if (isset($s)) {
            $school = School::findOne($s);
            $loginTitle = $school['login_title'];
        }

        $layoutHelper = new GuestLayoutHelper($school);
        $this->view->params['layoutHelper'] = $layoutHelper;

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
            'loginTitle' => $loginTitle,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignUp($s, $l)
    {
        $this->layout = '@app/views/layouts/signup';
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

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
                return $this->redirect(Url::to(["site/rent", 's' => $s, 'l' => $l]));
            }

            $user = $model->signUp();
            if ($user && SchoolStudent::createNew($s, $user->id)) {
                Yii::$app->user->login($user);

                RegistrationLesson::assignToStudent($s, $user->id, $model);
                EmailSender::sendNewStudentNotification($user, $school['email']);

                $chatMessage = RegistrationMessage::getBody($s, $model->ownsInstrument, $model->hasExperience);
                if ($chatMessage) {
                    Chat::addNewMessage($chatMessage, $schoolTeacher['id'], $user['id']);
                }

                if ($school['registration_message'] != null && $model->ownsInstrument) {
                    EmailSender::sendPostSignupMessage($school['registration_message'], $school['email'], $user['email']);
                }

                if ($model->hasExperience) {
                    $this->redirect(["signup-questions", 'u' => $user['id'], 's' => $s]);
                } else {
                    Yii::$app->session->setFlash('success', 'Hei! Esi veiksmīgi piereģistrējies. Noskaties iepazīšanās video ar platformu un sākam spēlēt! Turpmākās 2 nedēļas vari izmēģināt bez maksas!');
                    return $this->redirect(['lekcijas/index']);
                }
            }
        }

        $model->password = '';
        $model->passwordRepeat = '';
        $instrument = strtolower($school['instrument']);
        return $this->render('signup', [
            'model' => $model,
            'registration_title' => $school['registration_title'],
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
        $model = RentForm::createFromSession($signupModel);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = RentForm::registerUser($signupModel, $model->phone_number);

            if ($user && SchoolStudent::createNew($s, $user->id)) {
                RegistrationLesson::assignToStudent($s, $user->id, $signupModel);

                $chatMessage = RegistrationMessage::getBody($s, $signupModel->ownsInstrument, $signupModel->hasExperience);
                if ($chatMessage) {
                    Chat::addNewMessage($chatMessage, $schoolTeacher['id'], $user['id']);
                }

                if ($school['renter_message'] != null && $school['rent_schoolsubplan_id'] != null) {
                    $studentSubplan = RentForm::registerPlanForUser($user->id, $school['rent_schoolsubplan_id']);
                    InvoiceManager::sendAdvanceInvoice($user, $studentSubplan, true);
                }

                $sent = EmailSender::sendRentNotification($model, $school['email']);

                if ($sent) {
                    Yii::$app->session->setFlash('success', \Yii::t('app', 'Thank you for applying! Soon we will contact you by email. Until then you can watch our video tutorial about this platform') . '!');
                    Yii::$app->user->login($user, 3600 * 24 * 30);
                    return $this->redirect(['lekcijas/index']);
                }
            }
        }

        return $this->render('rent', [
            'model' => $model,
            'backUrl' => Url::to(['site/sign-up', 's' => $s, 'l' => $l]),
        ]);
    }

    public function actionSignupQuestions($u, $s)
    {
        $user = Users::findOne($u);
        $schoolSignupQuestions = SignupQuestions::getForSchool($s);

        $post = Yii::$app->request->post();
        if ($post && isset($post['answers']) && count($post['answers']) > 0) {
            $aboutUser = "";
            foreach ($post['answers'] as $answer) {
                if ($answer !== "") {
                    $aboutUser .= $answer;
                    $aboutUser .= "\n";
                }
            }
            $user['about'] = $aboutUser;
            $saved = $user->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', 'Hei! Esi veiksmīgi piereģistrējies. Noskaties iepazīšanās video ar platformu un sākam spēlēt! Turpmākās 2 nedēļas vari izmēģināt bez maksas!');
                return $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('signup-questions', [
            'questions' => $schoolSignupQuestions,
        ]);
    }

    public function actionMainpage()
    {
        $current = Yii::$app->user->identity;

        if (Yii::$app->user->isGuest) {
            $url = "site/login";
        } else if (Users::isAdminOrTeacher($current->email)) {
            $url = "lectures/index";
        } else {
            $url = "lekcijas/index";
        }

        return $this->redirect([$url]);
    }
}
