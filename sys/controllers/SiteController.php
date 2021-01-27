<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RentOrBuyForm;
use app\models\Lectures;
use app\models\SchoolTeacher;
use app\models\UserLectures;
use app\models\SignupQuestions;
use app\models\Evaluations;
use app\models\Lecturesevaluations;
use app\models\SignUpForm;
use app\models\SchoolStudent;
use app\models\School;
use app\models\RegistrationLesson;
use app\models\Users;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\ResendVerificationEmailForm;
use app\models\VerifyEmailForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Pārbaudiet savu e-pastu, lai turpinātu paroles atjaunošanas procesu.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Neizdevās nosūtīt e-pasta adresi, lai atjaunotu paroli.');
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }
        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
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

    public function actionLogin()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignUp($s, $l)
    {
        $this->layout = '@app/views/layouts/signup';
        if(!Yii::$app->user->isGuest) Yii::$app->user->logout();

        Yii::$app->language = $l;

        $school = School::findOne($s);

        $model = new SignUpForm();
        if ($model->load(Yii::$app->request->post())) {
            $model['language'] = $l;
            $userId = $model->signUp();
            $hasOwnInstrument = Yii::$app->request->post() && Yii::$app->request->post()['has-own-instrument'];
            $hasExperience = Yii::$app->request->post() && Yii::$app->request->post()['has-experience'];

            if($userId){
                $schoolStudent = new SchoolStudent;

                $schoolStudent->school_id = $s;
                $schoolStudent->user_id = $userId;

                $saved = $schoolStudent->save();

                if($saved){
                    $user = Users::findById($userId);
                    Yii::$app->user->login($user);

                    $schoolTeacher = SchoolTeacher::getBySchoolId($s)["user"];
                    $firstLectureIds = RegistrationLesson::getLessonIds($school['id'], $hasExperience);
                    $insertDate = date('Y-m-d H:i:s', time());
                    $insertColumns = [];

                    foreach($firstLectureIds as $lid){
                        $insertColumns[] = [$schoolTeacher["id"], $userId, $lid, $insertDate, 0, 0, 1];
                    }

                    Yii::$app->db
                        ->createCommand()
                        ->batchInsert('userlectures', ['assigned', 'user_id', 'lecture_id', 'created', 'user_difficulty', 'open_times', 'sent'], $insertColumns)
                        ->execute();

                    Yii::$app
                        ->mailer
                        ->compose(['html' => 'new-user-html', 'text' => 'new-user-text'], [
                            'user' => $user,
                        ])
                        ->setFrom([$school['email'] => Yii::$app->name])
                        ->setTo($school['email'])
                        ->setSubject("Reģistrējies jauns skolēns - " . $user['first_name'])
                        ->send();

                    if($school['registration_message'] != null && $hasOwnInstrument){
                        Yii::$app
                            ->mailer
                            ->compose(['html' => 'after-registration-html', 'text' => 'after-registration-text'], [
                                'message' => $school['registration_message'],
                            ])
                            ->setFrom([$school['email'] => Yii::$app->name])
                            ->setTo($user['email'])
                            ->setSubject("Apsveicam ar reģistrēšanos - " . Yii::$app->name)
                            ->send();
                    }

                    if(!$hasOwnInstrument){
                        $this->redirect(["rent-or-buy", 'u' => $user['id'], 'l' => $l]);
                    } else if($hasExperience) {
                        $this->redirect(["signup-questions", 'u' => $user['id'], 'l' => $l, 's' => $s]);
                    }else {
                        Yii::$app->session->setFlash('success', 'Hei! Esi veiksmīgi piereģistrējies. Noskaties iepazīšanās video ar platformu un sākam koklēt! Turpmākās 2 nedēļas vari izmēģināt bez maksas!');
                        return $this->redirect(['lekcijas/index']);
                    }
                }
            }
        }

        $model->password = '';
        return $this->render('signup', [
            'model' => $model,
            'defaultLanguage' => $l,
        ]);
    }

    public function actionRentOrBuy($u, $l) {
        Yii::$app->language = $l;
        $school = School::getByStudent($u);

        $user = Users::findOne($u);
        $model = new RentOrBuyForm;
        $model->fullname = $user['first_name'] . " " . $user['last_name'];
        $model->email = $user['email'];
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = Yii::$app
                ->mailer
                ->compose(['html' => 'instrument-html', 'text' => 'instrument-text'], [
                    'model' => $model,
                ])
                ->setFrom([$school['email'] => Yii::$app->name])
                ->setTo($school['email'])
                ->setSubject("Par kokles iegādāšanos - " . $model['fullname'])
                ->send();
            if($sent){
                Yii::$app->session->setFlash('success', 'Paldies par tavu pieteikumu! Tuvākajā laikā sazināsimies ar tevi uz tavu norādīto epastu. ');
                return $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('rent-or-buy', [
            'model' => $model,
        ]);
    }

    public function actionSignupQuestions($u, $l, $s){
        Yii::$app->language = $l;
        $user = Users::findOne($u);
        
        $schoolSignupQuestions = SignupQuestions::getForSchool($s);

        $post = Yii::$app->request->post();
        if($post && isset($post['answers']) && count($post['answers']) > 0){
            $aboutUser = "";
            foreach($post['answers'] as $id => $answer){
                if($answer !== ""){
                    $aboutUser .= $answer;
                    $aboutUser .= "\n";
                }
            }
            $user['about'] = $aboutUser;
            $saved = $user->save();
            if($saved){
                return $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('signup-questions', [
            'questions' => $schoolSignupQuestions,
        ]);
    }

    public function actionFirstLecture($l, $u){
        $lectureId = 192;
        Yii::$app->language = $l;

        $lecture = Lectures::findOne($lectureId);
        $evaluations = Evaluations::getEvaluations();
        $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($lectureId);
        foreach ($evaluations as &$evaluation) {
            if ($evaluation['star_text']) {
                $starTextArray = unserialize($evaluation['star_text']);
                foreach ($starTextArray as &$starText) {
                    $starText = Yii::t('app', $starText);
                };
                $evaluation['star_text'] = serialize($starTextArray);
            }
        }
        return $this->render('first-lecture', [
            'model' => $lecture,
            'evaluations' => $evaluations,
            'lectureEvaluations' => $lectureEvaluations,
        ]);
    }
}
