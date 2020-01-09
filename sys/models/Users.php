<?php

namespace app\models;

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
    const MAX_MORE_REQUESTS = 4;

    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['email'], 'required'],
            [['user_level', 'goal'], 'string'],
            ['user_level', 'default', 'value' => self::ROLE_USER],
            ['user_level', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['phone_number'], 'string', 'max' => 30],
            [['password', 'first_name', 'last_name'], 'string', 'max' => 250],
            [['email'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_level' => 'Piekļuves līmenis',
            'email' => 'E-pasts',
            'phone_number' => 'Telefona numurs',
            'first_name' => 'Vārds',
            'last_name' => 'Uzvārds',
            'password' => 'Parole',
            'last_login' => 'Pierakstījies',
            'last_lecture' => 'Pēdējā lekcija',
            'dont_bother' => 'Netraucēt',
            'status' => 'Statuss',
            'goal' => 'Mērķis',
            //more_lecture_requests` tikai līdz 4 uzdevumiem starp sūtīšanas reizēm. Lai neaptrūkstas uzdevumi
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getAdmins()
    {
        return ArrayHelper::map(self::find()->where(['user_level' => self::ROLE_ADMIN])->asArray()->all(), 'id', 'email');
    }

    /**
     * {@inheritdoc}
     */
    public static function getActiveStudents($dont_bother = false)
    {
        $params = ['user_level' => self::ROLE_USER, 'status' => self::STATUS_ACTIVE];
        if ($dont_bother) {
            $users = self::find()->where($params)->asArray()->all();
            $result = [];
            foreach ($users as $u) {
                if ($u['dont_bother'] != null) {
                    $time = time();
                    $check = strtotime($u['dont_bother']);
                    if ($check < $time) {
                        $result[$u['id']] = $u;
                    }
                } else {
                    $result[$u['id']] = $u;
                }
            }
        } else {
            $users = ArrayHelper::map(self::find()->where($params)->asArray()->all(), 'id', 'email');
        }
        return $dont_bother ? $result : $users;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
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
            self::ROLE_USER => 'Students',
            self::ROLE_ADMIN => 'Administrators',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'last_lecture']);
    }

}
