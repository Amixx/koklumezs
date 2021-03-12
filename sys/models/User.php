<?php

namespace app\models;

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
            [['user_level'], 'string'],
            ['user_level', 'default', 'value' => self::ROLE_USER],
            ['user_level', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_TEACHER]],
            [['email'], 'email'],
            [['username'], 'unique'],
            [['phone_number'], 'string', 'max' => 30],
            [['password', 'first_name', 'last_name'], 'string', 'max' => 250],
            [['email'], 'string', 'max' => 500],
            [['username'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_level' => \Yii::t('app',  'Access level'),
            'email' => \Yii::t('app',  'E-mail'),
            'username' => \Yii::t('app',  'Username'),
            'phone_number' => \Yii::t('app',  'Phone number'),
            'first_name' =>  \Yii::t('app',  'Name'),
            'last_name' => \Yii::t('app',  'Surname'),
            'password' => \Yii::t('app',  'Password'),
            'about' => \Yii::t('app',  'About user'),
            'last_opened_chat' => \Yii::t('app',  'Last time opened chat'),
            'wants_more_lessons' => \Yii::t('app',  'Wants more lessons'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [self::STATUS_ACTIVE, self::STATUS_PASSIVE]]);
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
        ]);
    }

    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
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

    public static function isUserAdmin($email)
    {
        if (static::findOne(['email' => $email, 'user_level' => self::ROLE_ADMIN])) {
            return true;
        } else {
            return false;
        }
    }

    public static function isStudent($email)
    {
        if (static::findOne(['email' => $email, 'user_level' => self::ROLE_USER])) {
            return true;
        } else {
            return false;
        }
    }

    public static function isTeacher($email)
    {
        if (static::findOne(['email' => $email, 'user_level' => self::ROLE_TEACHER])) {
            return true;
        } else {
            return false;
        }
    }

    public static function getStatus()
    {
        return [
            self::STATUS_INACTIVE => 'Nav aktīvs',
            self::STATUS_ACTIVE => 'Aktīvs',
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

    public function getAuthors()
    {
        return $this->hasMany(Projects::className(), ['created_by' => 'id']);
    }

    public function getChanges()
    {
        return $this->hasMany(Projects::className(), ['change_by' => 'id']);
    }
}
