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

    public function actionSignUp($s, $l)
    {
        return $this->redirect(Url::to(["registration/index", 's' => $s, 'l' => $l]));
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = '@app/views/layouts/login';
        $layoutHelper = new GuestLayoutHelper(null);
        $this->view->params['layoutHelper'] = $layoutHelper;

        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Check your email to continue the recovery process') . '!');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Couldn\'t send email to recover password') . '.');
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
            Yii::$app->session->setFlash('success', Yii::t('app', 'New password saved') . '.');
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
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your email has been confirmed') . '!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we are unable to verify your account with provided token.') . '.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Check your email for further instructions') . '.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we are unable to resend verification email for the provided email address') . '.');
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
