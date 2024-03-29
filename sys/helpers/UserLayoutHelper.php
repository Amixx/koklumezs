<?php

namespace app\helpers;

use app\models\Chat;
use yii\helpers\Html;
use app\models\Users;
use app\models\School;
use app\models\SchoolStudent;
use app\models\SchoolTeacher;
use app\models\StartLaterCommitments;
use Yii;

class UserLayoutHelper extends LayoutHelper
{
    public $isAdmin;
    private $isTeacher;
    private $isStudent;

    public function __construct()
    {
        $userContext = Yii::$app->user->identity;

        $this->school = $userContext->getSchool();
        $this->isAdmin = $userContext->isAdmin();
        $this->isTeacher = $userContext->isTeacher();
        $this->isStudent = $userContext->isStudent();
    }

    public function getNavItems()
    {
        $navItems = [];
        $userTypeNavItems = $this->getUserTypeNavItems();

        if ($this->isAdmin) {
            $navItems = $userTypeNavItems['admin'];
        } elseif ($this->isTeacher) {
            $navItems = $userTypeNavItems['teacher'];
        } elseif ($this->isStudent) {
            $navItems = $userTypeNavItems['student'];
        }

        $navItems[] = $this->getNavEnd();

        return $navItems;
    }

    public function shouldRenderChat()
    {
        return !$this->isAdmin;
    }

    public function getLogoClass()
    {
        $class = "school-logo";
        if ($this->isAdmin) {
            $class .= " admin";
        }

        return $class;
    }

    public function getNavbarClass()
    {
        $class = "navbar-inverse navbar-fixed-top";
        if ($this->isAdmin) {
            $class .= " navbar-admin";
        }

        return $class;
    }

    private function getNavEnd()
    {
        $fullName = Users::getFullName(Yii::$app->user->identity);

        return '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app',  'Sign out') . " <br> ($fullName)",
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    private function getUserTypeNavItems()
    {
        $userContext = Yii::$app->user->identity;
        $hasStudents = $userContext->isTeacher() && count(Users::getStudentsWithoutPausesForSchool()) > 0;
        $isFitnessSchool = !$this->isAdmin && $userContext->getSchool()->is_fitness_school;

        $fitnessTeacherMenu =
            [
                ['label' => 'GYM', 'url' => ['/assign'], 'active' =>  in_array(Yii::$app->controller->id, ['assign']),],
                ['label' => '+', 'url' => ['/assign/userlectures'], 'active' =>  in_array(Yii::$app->controller->id, ['assign'])],
                ['label' => Yii::t('app',  'Clients'), 'url' => ['/user'], 'active' =>  in_array(Yii::$app->controller->id, ['user'])],
                ['label' => '+', 'url' => ['/user/create'], 'active' =>  in_array(Yii::$app->controller->id, ['user'])],
                ['label' => Yii::t('app',  'Exercises'), 'url' => ['/fitness-exercises'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-exercises']),],
                [
                    'label' => '+',
                    'url' => ['/fitness-exercises/create'],
                    'active' => in_array(Yii::$app->controller->id, ['fitness-exercises']),
                    'linkOptions' => ['target' => '_blank']
                ],
                ['label' => Yii::t('app',  'Templates'), 'url' => ['/fitness-templates'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-templates']),],
                ['label' => '+', 'url' => ['/fitness-templates/create'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-templates']),],
                ['label' => Yii::t('app',  'Tags'), 'url' => ['/fitness-tags'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-tags']),],
                ['label' => '+', 'url' => ['/fitness-tags/create'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-tags']),],
                ['label' => Yii::t('app',  'Progression chains'), 'url' => ['/fitness-progression-chains'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-progression-chains']),],
                ['label' => '+', 'url' => ['/fitness-progression-chains/create'], 'active' =>  in_array(Yii::$app->controller->id, ['fitness-progression-chains']),],
                ['label' => Yii::t('app',  'Settings'), 'url' => ['/school-settings'], 'active' =>  in_array(Yii::$app->controller->id, ['school-settings'])],
                ['label' => Yii::t('app',  'WEAR'), 'url' => ['/weight-exercise-ability-ratios'], 'active' => in_array(Yii::$app->controller->id, ['weight-exercise-ability-ratios'])],
            ];

        $instrumentTeacherMenu =
            [
                ['label' => Yii::t('app',  'School'), 'url' => ['/assign'], 'active' =>  in_array(Yii::$app->controller->id, ['assign']),],
                $hasStudents ? ['label' => '+', 'url' => ['/assign/userlectures'], 'active' =>  in_array(Yii::$app->controller->id, ['assign'])] : ['label' => '', 'url' => ['/assign']],
                ['label' => Yii::t('app',  'Students'), 'url' => ['/user'], 'active' =>  in_array(Yii::$app->controller->id, ['user'])],
                ['label' => '+', 'url' => ['/user/create'], 'active' =>  in_array(Yii::$app->controller->id, ['user'])],
                ['label' => Yii::t('app',  'Lessons'), 'url' => ['/lectures'], 'active' =>  in_array(Yii::$app->controller->id, ['lectures']),],
                ['label' => '+', 'url' => ['/lectures/create'], 'active' =>  in_array(Yii::$app->controller->id, ['lectures']),],
                [
                    'label' => Yii::t('app',  'Subscription plans'),
                    'url' => ['/school-sub-plans'],
                    'active' =>  in_array(Yii::$app->controller->id, ['school-sub-plans']),
                    'items' => [
                        ['label' => Yii::t('app',  'Subscription plans'), 'url' => ['/school-sub-plans']],
                        ['label' => Yii::t('app',  'Plan parts'), 'url' => ['/plan-parts']],
                        ['label' => Yii::t('app',  'Plan pauses'), 'url' => ['/student-subplan-pauses']],
                    ],
                    'options' => ['class' => 'nav-item dropdown']
                ],
                ['label' => '+', 'url' => ['/school-sub-plans/create'], 'active' =>  in_array(Yii::$app->controller->id, ['school-sub-plans']),],
                [
                    'label' => Yii::t('app',  'Metrics'),
                    'active' =>  in_array(Yii::$app->controller->id, ['user-lecture-evaluations']) && Yii::$app->controller->action->actionMethod != "actionComments",
                    'items' => [
                        ['label' => Yii::t('app',  'Student evaluations'), 'url' => ['/user-lecture-evaluations']],
                        ['label' => Yii::t('app',  'Sent invoices'), 'url' => ['/sent-invoices']],
                    ],
                    'options' => ['class' => 'nav-item dropdown']
                ],
                ['label' => Yii::t('app',  'Settings'), 'url' => ['/school-settings'], 'active' =>  in_array(Yii::$app->controller->id, ['school-settings'])],
            ];

        $data = [
            'admin' => [
                ['label' => 'Piešķiršana', 'url' => ['/assign'], 'active' =>  in_array(Yii::$app->controller->id, ['assign']),],
                ['label' => 'Piešķirts', 'url' => ['/user-lectures'], 'active' =>  in_array(Yii::$app->controller->id, ['user-lectures']),],
                ['label' => Yii::t('app',  'Lessons'), 'url' => ['/lectures'], 'active' =>  in_array(Yii::$app->controller->id, ['lectures']),],
                ['label' => 'Faili', 'url' => ['/lecturesfiles'], 'active' =>  in_array(Yii::$app->controller->id, ['lecturesfiles']),],
                ['label' => 'Parametri', 'url' => ['/difficulties'], 'active' =>  in_array(Yii::$app->controller->id, ['difficulties']),],
                ['label' => Yii::t('app',  'Section visibility'), 'url' => ['/sections'], 'active' =>  in_array(Yii::$app->controller->id, ['sections']),],
                ['label' => Yii::t('app',  'Evaluations'), 'url' => ['/evaluations'], 'active' =>  in_array(Yii::$app->controller->id, ['evaluations'])],
                ['label' => 'Lietotāji', 'url' => ['/user'], 'active' =>  in_array(Yii::$app->controller->id, ['user'])],
                ['label' => 'Lietotāju vērtējumi', 'url' => ['/user-lecture-evaluations'], 'active' =>  in_array(Yii::$app->controller->id, ['user-lecture-evaluations'])],
                ['label' => 'Izsūtītie e-pasti', 'url' => ['/sentlectures'], 'active' =>  in_array(Yii::$app->controller->id, ['sentlectures'])],
                [
                    'label' => Yii::t('app',  'Sent invoices'),
                    'url' => ['/sent-invoices'],
                    'active' =>  in_array(Yii::$app->controller->id, ['sent-invoices']),
                ],
            ],
            'teacher' => $isFitnessSchool ? $fitnessTeacherMenu : $instrumentTeacherMenu,
            'student' => [
                [
                    'label' => Yii::t('app',  $isFitnessSchool ? 'Workouts' : 'Lessons'),
                    'active' =>  in_array(Yii::$app->controller->id, ['lekcijas']),
                    'items' => [
                        ['label' => Yii::t('app', $isFitnessSchool ? 'New workouts' : 'New lessons'), 'url' => ['/lekcijas?type=new&sortType=1']],
                        ['label' => Yii::t('app',  'Favourite lessons'), 'url' => ['/lekcijas?type=favourite&sortType=1']]
                    ],
                    'options' => ['class' => 'navbar-lessons-dropdown-toggle']
                ],
                // ['label' => Yii::t('app',  'acords'), 'url' => ['/'], 'active' =>  in_array(Yii::$app->controller->id, [''])],
                // ['label' => Yii::t('app',  'play along'), 'url' => ['/'], 'active' =>  in_array(Yii::$app->controller->id, [''])],
                ['label' => Yii::t('app',  'Archive'), 'url' => ['/archive'], 'active' =>  in_array(Yii::$app->controller->id, ['archive'])],
                // ['label' => Yii::t('app',  'suggest a song'), 'url' => ['/'], 'active' =>  in_array(Yii::$app->controller->id, [''])],
                ['label' => Yii::t('app',  'FAQs'), 'url' => ['/school-faqs/for-students'], 'active' =>  in_array(Yii::$app->controller->id, ['school-faqs'])],
                ['label' => Yii::t('app',  'Subscription plan'), 'url' => ['/student-sub-plans/for-user/?studentId=' . Yii::$app->user->identity->id], 'active' =>  in_array(Yii::$app->controller->id, ['student-sub-plans'])],
                // ['label' => Yii::t('app',  'Join another school'), 'url' => ['/join-school'], 'active' =>  in_array(Yii::$app->controller->id, ['join-school'])],
            ],
        ];

        if (!$isFitnessSchool) {
            $sheetMusicItem = ['label' => Yii::t('app',  'Sheet music'), 'url' => ['/file'], 'active' =>  in_array(Yii::$app->controller->id, ['file'])];
            $previous_items = array_slice($data['student'], 0, 1, true);
            $next_items     = array_slice($data['student'], 1, NULL, true);

            $data['student'] = array_merge($previous_items, [$sheetMusicItem], $next_items);
        }

        if ($userContext->is_test_user) {
            $data['student'][] = ['label' => Yii::t('app',  'Invoices'), 'url' => ['/student-invoices'], 'active' =>  in_array(Yii::$app->controller->id, ['student-invoices'])];
        }

        return $data;
    }

    public function getActionButton()
    {
        if ($this->isTeacher || $this->isAdmin) return "";

        $userId = Yii::$app->user->identity->id;

        $schoolStudent = SchoolStudent::getSchoolStudent($userId);
        $startLaterCommitment = StartLaterCommitments::findOne(['user_id' => $userId]);

        if ($schoolStudent['signed_up_to_rent_instrument'] && !$schoolStudent['has_instrument']) {
            return Html::a(
                Yii::t('app', 'I have received the instrument'),
                ['registration/received-instrument'],
                ['class' => 'btn btn-orange btn-received-instrument']
            );
        } else if (!$schoolStudent['show_real_lessons'] && $startLaterCommitment && !$startLaterCommitment['chosen_period_started']) {
            return Html::a(
                Yii::t('app', 'I want to start playing now!'),
                ['user/start-immediately'],
                ['class' => 'btn btn-orange btn-received-instrument']
            );
        }
    }

    public function getChatButton()
    {
        if ($this->isAdmin) return "";

        $chatButtonText = "Chat";
        $outerClass = "";
        $buttonClasses = "btn btn-success teacher-communication-button";
        $buttonStyle = "unset";

        ['messages' => $unreadMessages, 'conversations' => $unreadConversations] = Chat::unreadDataForCurrentUser();

        $unreadConversationsHtml = $unreadConversations > 1
            ? "<span class='chat-unread-count-groups'>$unreadConversations</span>"
            : "";
        $unreadMessagesHtml = $unreadMessages > 0
            ? '<span class="chat-unread-count">' . $unreadMessages . '</span>'
            : "";

        if ($this->isTeacher) {
            $chatButtonText = "Chat with students";
            $outerClass = "teacher";
        } else {
            $teacherPortrait = $this->school['teacher_portrait'];
            if ($teacherPortrait) {
                $buttonClasses .= " with-portrait";
                $buttonStyle = "background-image: url($teacherPortrait)";
            }
        }

        return '<div id="chat-btn-with-icons" class="' . $outerClass . '">'
            . '<div id="notification-badges">'
            . $unreadConversationsHtml
            . $unreadMessagesHtml
            . '</div>'
            . '<button class="' . $buttonClasses . '" id="chat-toggle-button" style="' . $buttonStyle . '" data-toggle="modal" data-target="#chatModal">'
            . Yii::t('app', $chatButtonText)
            . '</button>'
            . '</div>';
    }
}
