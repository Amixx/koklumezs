<?php

namespace app\models;

use app\fitness\models\ClientData;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 'Student';
    const ROLE_ADMIN = 'Admin';
    const ROLE_TEACHER = 'Teacher';

    const MAX_MORE_REQUESTS = 4;

    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_PASSIVE = 11;

    const LANG_LV = 'lv';
    const LANG_ENG = 'eng';

    const SUBTYPE_FREE = 'free';
    const SUBTYPE_PAID = 'paid';
    const SUBTYPE_LEAD = 'lead';

    public $authKey;

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_PASSIVE]],
            [['user_level', 'stripe_id'], 'string'],
            ['user_level', 'default', 'value' => self::ROLE_USER],
            ['user_level', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_TEACHER]],
            ['language', 'default', 'value' => self::LANG_LV],
            ['language', 'in', 'range' => [self::LANG_LV, self::LANG_ENG]],
            ['subscription_type', 'default', 'value' => self::SUBTYPE_LEAD],
            ['subscription_type', 'in', 'range' => [self::SUBTYPE_FREE, self::SUBTYPE_PAID, self::SUBTYPE_LEAD]],
            [['email'], 'email'],
            [['phone_number'], 'string', 'max' => 30],
            [['password', 'first_name', 'last_name'], 'string', 'max' => 250],
            [['email'], 'string', 'max' => 500],
            [['is_test_user'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stripe_id' => 'Stripe ID',
            'user_level' => \Yii::t('app',  'Access level'),
            'language' => \Yii::t('app',  'User language'),
            'subscription_type' => \Yii::t('app',  'Abonement type'),
            'email' => \Yii::t('app',  'E-mail'),
            'phone_number' => \Yii::t('app',  'Phone number'),
            'first_name' => \Yii::t('app',  'Name'),
            'last_name' => \Yii::t('app',  'Surname'),
            'password' => \Yii::t('app',  'Password'),
            'about' => \Yii::t('app',  'Notes'),
            'last_login' => \Yii::t('app',  'Last logged in'),
            'last_lecture' => \Yii::t('app',  'Last lesson'),
            'status' => \Yii::t('app',  'Status'),
            'allowed_to_download_files' => \Yii::t('app',  'Allowed to download files'),
            'wants_more_lessons' => \Yii::t('app',  'Wants more lessons'),
            'is_deleted' => \Yii::t('app',  'Is deleted'),
            'is_test_user' => \Yii::t('app',  'Is test user'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function getPayer()
    {
        return $this->hasOne(Payer::class, ['user_id' => 'id']);
    }

    public function getSchoolStudent()
    {
        return $this->hasOne(SchoolStudent::class, ['user_id' => 'id']);
    }

    public function getClientData()
    {
        return $this->hasOne(ClientData::class, ['user_id' => 'id']);
    }

    public function getTrial()
    {
        return $this->hasOne(Trials::class, ['user_id' => 'id']);
    }


    public function getReceivedChatMessages()
    {
        return $this->hasMany(Chat::class, ['recipient_id' => 'id'])->orderBy(['id' => SORT_DESC])->alias("received");
    }

    public function getSentChatMessages()
    {
        return $this->hasMany(Chat::class, ['author_id' => 'id'])->orderBy(['id' => SORT_DESC])->alias("sent");
    }

    public function getCorrespondenceOpenTimes()
    {
        return $this->hasMany(CorrespondenceOpentimes::class, ['author_id' => 'id']);
    }

    public function getLatestConversations()
    {
        $latestMessageInEachConversation = Chat::find()
            ->where("update_date in (
                SELECT max(update_date)
                FROM chat where author_id = $this->id or recipient_id = $this->id
                GROUP BY author_id, recipient_id
            )")
            ->orderBy('update_date desc')
            ->limit(30)
            ->all();

        $latestConversations = [];

        foreach ($latestMessageInEachConversation as $msg) {
            $currentUserIsAuthor = $msg->author_id == $this->id;

            $user = $currentUserIsAuthor
                ? $msg->recipient
                : $msg->author;

            if (!$user || $user->is_deleted) continue;

            $userAlreadyAdded = !empty(array_filter($latestConversations, function ($conv) use ($user) {
                return $conv['user']->id === $user->id;
            }));

            if ($userAlreadyAdded) continue;

            $isUnreadByCurrentUser = !$currentUserIsAuthor && $msg->isUnread();

            $conversationToAdd = [
                'user' => $user,
                'is_unread' => $isUnreadByCurrentUser,
            ];

            $isUnreadByCurrentUser // ja current useris nav lasījis pēdējo ziņu, tad sarakste ir augšā
                ? array_unshift($latestConversations, $conversationToAdd)
                : array_push($latestConversations, $conversationToAdd);
        }

        return $latestConversations;
    }

    public function updateLoginTime()
    {
        $this->last_login = date('Y-m-d H:i:s', time());
        return $this->update();
    }

    public static function getFullName($user)
    {
        return $user['first_name'] . " " . $user['last_name'];
    }

    public static function getByEmail($email)
    {
        return static::findOne(['email' => $email, 'is_deleted' => false]);
    }

    public static function isActive($id)
    {
        $status = static::findOne(['id' => $id, 'is_deleted' => false])->status;
        return $status != 9;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function getAdmins()
    {
        return ArrayHelper::map(self::find()->where(['user_level' => self::ROLE_ADMIN, 'is_deleted' => false])->asArray()->all(), 'id', 'email');
    }

    public static function getTeachers()
    {
        return ArrayHelper::map(self::find()->where(['user_level' => self::ROLE_TEACHER, 'is_deleted' => false])->asArray()->all(), 'id', 'email');
    }

    public static function getActiveStudents()
    {
        $params = ['user_level' => self::ROLE_USER, 'status' => self::STATUS_ACTIVE, 'is_deleted' => false];
        $users = self::find()->where($params)->asArray()->all();

        $result = [];
        foreach ($users as $u) {
            $result[$u['id']] = $u;
        }
        return $result;
    }

    public static function getActiveStudentEmails()
    {
        $students = self::getActiveStudents();
        return ArrayHelper::map($students, 'id', 'email');
    }

    public static function getAllStudents()
    {
        $users = self::find()->where(['user_level' => self::ROLE_USER, 'is_deleted' => false])->joinWith("payer")->asArray()->all();
        $result = [];
        foreach ($users as $u) {
            $result[$u['id']] = $u;
        }

        return $result;
    }

    public static function getStudents()
    {
        $params = ['user_level' => self::ROLE_USER, 'status' => [self::STATUS_ACTIVE, self::STATUS_PASSIVE], 'is_deleted' => false];
        $users = self::find()->where($params)->asArray()->all();

        $result = [];
        foreach ($users as $u) {
            $result[$u['id']] = $u;
        }
    }

    public static function getStudentsWithoutPausesForSchool()
    {
        $students = self::getStudentsForSchool(false);
        foreach ($students as $key => $student) {
            $isCurrentLearningPlanPaused = StudentSubPlans::isCurrentLearningPlanPaused($student['id']);
            if ($isCurrentLearningPlanPaused) {
                unset($students[$key]);
            }
        }

        return $students;
    }

    public static function getStudentsForSchool($includePassive = true)
    {
        $params = ['user_level' => self::ROLE_USER, 'status' => [self::STATUS_ACTIVE], 'is_deleted' => false];
        if ($includePassive) {
            $params['status'][] = self::STATUS_PASSIVE;
        }
        $userContext = Yii::$app->user->identity;
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($userContext->getSchool()->id);
        $usersData = self::find()->where($params)->andWhere(['in', 'id', $schoolStudentIds])->asArray()->all();

        $result = [];
        foreach ($usersData as $u) {
            $result[$u['id']] = $u;
        }

        return $result;
    }

    public static function getStudentNamesForSchool()
    {
        $students = self::getStudentsForSchool();
        $studentNames = [];

        foreach ($students as $student) {
            $studentNames[$student['id']] = $student['first_name'] . ' ' . $student['last_name'];
        }

        return $studentNames;
    }

    public static function getStudentsWithParams($lang, $subTypes)
    {
        $params = ['user_level' => self::ROLE_USER, 'status' => [self::STATUS_ACTIVE], 'is_deleted' => false];

        $userContext = Yii::$app->user->identity;
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($userContext->getSchool()->id);

        if ($lang) {
            $params['language'] = $lang;
        }
        if ($subTypes && in_array("pausing", $subTypes)) {
            array_push($params['status'], self::STATUS_PASSIVE);
        }
        $query = self::find()->where($params);
        $query->andWhere(['in', 'id', $schoolStudentIds]);
        if ($subTypes) {
            $query->andWhere(['in', 'subscription_type', $subTypes]);
        }

        $users = $query->asArray()->all();

        $result = [];
        foreach ($users as $u) {
            $isCurrentLearningPlanPaused = StudentSubPlans::isCurrentLearningPlanPaused($u['id']);
            if (!$isCurrentLearningPlanPaused) {
                $result[$u['id']] = $u;
            }
        }

        return $result;
    }

    public static function softDelete($userId)
    {
        $model = static::findOne(['id' => $userId]);
        $model->is_deleted = true;
        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByEmail($email)
    {
        $user = self::find()
            ->where([
                "email" => $email,
                'is_deleted' => false
            ])
            ->one();
        if (empty($user)) {
            return null;
        }
        return new static($user);
    }

    public static function findById($id)
    {
        $user = self::find()
            ->where([
                "id" => $id,
                'is_deleted' => false,
            ])
            ->one();
        if (empty($user)) {
            return null;
        }
        return new static($user);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function doesUserExist($firstName, $lastName, $email, $schoolId)
    {
        $data = self::find()->where([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'is_deleted' => false,
        ])->one();

        if ($data) {
            $schoolStudent = SchoolStudent::findOne(['user_id' => $data['id'], 'school_id' => $schoolId]);

            return $schoolStudent != null;
        }

        return false;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
        ]);
    }
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public static function isUserAdmin($email)
    {
        return (bool) static::findOne(['email' => $email, 'user_level' => self::ROLE_ADMIN, 'is_deleted' => false]);
    }

    public static function isStudent($email)
    {
        return (bool) static::findOne(['email' => $email, 'user_level' => self::ROLE_USER, 'is_deleted' => false]);
    }

    public static function isTeacher($email)
    {
        return (bool) static::findOne(['email' => $email, 'user_level' => self::ROLE_TEACHER, 'is_deleted' => false]);
    }

    public static function isCurrentUserTeacher()
    {
        if (!isset(Yii::$app->user->identity->email)) {
            return false;
        }
        return self::isTeacher(Yii::$app->user->identity->email);
    }

    public static function isCurrentUserStudent()
    {
        if (!isset(Yii::$app->user->identity->email)) {
            return false;
        }
        return self::isStudent(Yii::$app->user->identity->email);
    }

    public static function isAdminOrTeacher($email)
    {
        return (bool) static::findOne(['email' => $email, 'user_level' => [self::ROLE_ADMIN, self::ROLE_TEACHER], 'is_deleted' => false]);
    }

    public static function getStatus()
    {
        return [
            self::STATUS_INACTIVE => \Yii::t('app',  'Inactive'),
            self::STATUS_ACTIVE => \Yii::t('app',  'Active'),
            self::STATUS_PASSIVE => \Yii::t('app',  'Passive'),
        ];
    }

    public static function getLevels()
    {
        return [
            self::ROLE_USER => \Yii::t('app',  'Student'),
            self::ROLE_ADMIN => \Yii::t('app',  'Administrator'),
            self::ROLE_TEACHER => \Yii::t('app',  'Teacher'),
        ];
    }

    public static function getLanguages()
    {
        return [
            self::LANG_LV => \Yii::t('app', 'Latvian'),
            self::LANG_ENG => \Yii::t('app', 'English'),
        ];
    }

    public static function getSubscriptionTypes()
    {
        return [
            self::SUBTYPE_FREE => \Yii::t('app', 'For free'),
            self::SUBTYPE_PAID => \Yii::t('app', 'Paid'),
            self::SUBTYPE_LEAD => \Yii::t('app', 'Lead'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'last_lecture']);
    }
}
