<?php
 
namespace app\models;
use Yii;
 
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
 
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 'Student';
    const ROLE_ADMIN = 'Admin';    
 
    public static function tableName() { return 'users'; }
 
   /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['user_level'], 'string'],
            ['user_level', 'default', 'value' => self::ROLE_USER],
            ['user_level', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],	
            [['email'], 'email'],
            [['email'], 'unique'],
 			[['phone_number'],'string','max' => 30],
		    [['password','first_name','last_name'], 'string', 'max' => 250],
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
        ];
    }
 
    public static function findIdentity($id) {
        $user = self::find()
                ->where([
                    "id" => $id
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
    public static function findIdentityByAccessToken($token, $userType = null) {
    
        $user = self::find()
                ->where(["accessToken" => $token])
                ->one();
        if (empty($user)) {
            return null;
        }
        return new static($user);
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
        $user = self::find()
                ->where([
                    "email" => $username
                ])
                ->one();
        if (empty($user)) {
            return null;
        }
        return new static($user);
    }
    
    public static function findByUser($username) {
        $user = self::find()
                ->where([
                    "email" => $username
                ])
                ->one();
        if (empty($user)) {
            return null;
        }
        return $user;
    }
    
    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
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
 
    public static function isUserAdmin($username)
    {
        if (static::findOne(['email' => $username, 'user_level' => self::ROLE_ADMIN])){                            
            return true;
        } else {                            
            return false;
        }            
    }

    public static function isExpert($username)
    {
        if (static::findOne(['email' => $username, 'user_level' => self::ROLE_USER])){                            
            return true;
        } else {                            
            return false;
        }            
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
