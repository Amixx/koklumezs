<?php

namespace app\models;

use app\fitness\models\ClientData;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 'Student';
    const ROLE_ADMIN = 'Admin';
    const ROLE_TEACHER = 'Teacher';

    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_PASSIVE = 11;

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
            'email' => \Yii::t('app',  'E-mail'),
            'phone_number' => \Yii::t('app',  'Phone number'),
            'first_name' =>  \Yii::t('app',  'Name'),
            'last_name' => \Yii::t('app',  'Surname'),
            'password' => \Yii::t('app',  'Password'),
            'about' => \Yii::t('app',  'Notes'),
            'last_opened_chat' => \Yii::t('app',  'Last time opened chat'),
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

    public function getSchoolStudent()
    {
        return $this->hasOne(SchoolStudent::class, ['user_id' => 'id'])->joinWith("school");
    }

    public function getSchoolTeacher()
    {
        return $this->hasOne(SchoolTeacher::class, ['user_id' => 'id'])->joinWith("school");
    }

    public function getClientData()
    {
        return $this->hasOne(ClientData::class, ['user_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [self::STATUS_ACTIVE, self::STATUS_PASSIVE], 'is_deleted' => false]);
    }

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

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

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

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
            'is_deleted' => false
        ]);
    }

    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
            'is_deleted' => false
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public static function getStatus()
    {
        return [
            self::STATUS_INACTIVE => \Yii::t('app',  'Inactive'),
            self::STATUS_ACTIVE => \Yii::t('app',  'Active'),
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

    public function getSchool()
    {
        if ($this->isTeacher()) return $this->schoolTeacher->school;
        else if ($this->isStudent()) return $this->schoolStudent->school;

        return null;
        // throw new Exception("User is not a teacher or a student");
    }

    public function isTeacher()
    {
        return $this->user_level === self::ROLE_TEACHER;
    }

    public function isStudent()
    {
        return $this->user_level === self::ROLE_USER;
    }

    public function isAdmin()
    {
        return $this->user_level === self::ROLE_ADMIN;
    }
}
