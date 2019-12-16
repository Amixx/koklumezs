<?php

namespace app\models;

use app\models\Lectures;
use app\models\Users;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "userlectures".
 *
 * @property int $id
 * @property int $lecture_id Lekcija
 * @property int $user_id Students
 * @property int $assigned Administrators
 * @property string $createdtime Izveidots
 *
 * @property Users $assigned
 * @property Lectures $lecture
 */
class UserLectures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userlectures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'user_id', 'assigned'], 'required'],
            [['lecture_id', 'user_id', 'assigned', 'opened', 'sent'], 'integer'],
            [['created', 'opentime', 'sent', 'open_times', 'user_difficulty'], 'safe'],
            [['assigned'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['assigned' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => 'Lekcija',
            'user_id' => 'Students',
            'assigned' => 'Administrators',
            'created' => 'Izveidots',
            'opened' => 'Atvērta',
            'opentime' => 'Atvēršanas laiks',
            'sent' => 'Nosūtīts e-pasts',
            'evaluated' => 'Novērtēta',
            'user_difficulty' => 'Spējas'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Users::className(), ['id' => 'assigned'])
            ->from(['admin' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLectures($id, $sent = 1): array
    {
        //not anymore , 'sent' => $sent
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 0])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluatedLectures($id): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEvaluatedLecture($id)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTenEvaluatedLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTenLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDayResult($id, $days = 7)
    {
        $result = 0;
        $data = self::find()->where(['user_id' => $id])->andWhere('created >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)')->orderBy(['id' => SORT_DESC])->all();
        foreach($data as $d){
            $result += (int)$d->open_times;
        }
        return $result;
    }


    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEvaluatedLectures($ids)
    {
        return self::find()->where(['in', 'user_id', $ids])->andWhere(['evaluated' => 1])->orderBy(['id' => SORT_DESC])->asArray()->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLectureTimes($id): array
    {
        $results = self::find()->where(['user_id' => $id])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'created') : [];
    }

    public function getLastLecturesForUser($id, $limit = 5): array
    {
        $results = self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public function getLectures($id, $evaluated = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated])->orderBy(['id' => SORT_DESC])->all();
    }

    public function getUnsentLectures($id, $evaluated = 0, $sent = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated, 'sent' => $sent])->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function setSeenByUser($user_id, $id)
    {
        $setOpenTime = false;
        if (($user = Users::findOne($user_id)) !== null) {
            $model = self::find()->where(['opened' => 0, 'user_id' => $user_id, 'lecture_id' => $id])->one();
            if ($model) {
                $model->opened = 1;
                if (!$setOpenTime) {
                    $model->open_times = $model->open_times + 1;
                }
                $model->opentime = date('Y-m-d H:i:s', time());
                $setOpenTime = $model->update();
            } else {
                $model = self::find()->where(['opened' => 1, 'user_id' => $user_id, 'lecture_id' => $id])->one();
                if ($model and !$setOpenTime) {
                    $model->open_times = (int) $model->open_times + 1;
                    $setOpenTime = $model->update();
                }
            }
            $user->last_lecture = $id;
            $user->update();
        }
        return true;
    }

    public function getOpened($id)
    {
        $results = self::find()->where(['user_id' => $id, 'opened' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'id') : [];
    }

    public function getNewLectures($user_id, $ids = []): array
    {
        $ids = array_map('intval', $ids);
        $results = [];
        $r = self::find()->where(['user_id' => $user_id])->orderBy(['lecture_id' => SORT_ASC])->all();
        $current = $r ? ArrayHelper::map($r, 'id', 'lecture_id') : $results;
        if ($current) {
            $results = array_diff($ids, $current);
        }
        return $results;
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($id, $lecture_id)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }
        $lecture = Lectures::findOne($lecture_id);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'lekcija-html', 'text' => 'lekcija-text'],
                ['user' => $user, 'lecture' => $lecture]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Jauna lekcija - ' . Yii::$app->name)
            ->send();
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendAdminEmail($id, $lecture_id, $x)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }
        $lecture = Lectures::findOne($lecture_id);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'japieskir-lekcija-html', 'text' => 'japieskir-lekcija-text'],
                ['user' => $user, 'lecture' => $lecture, 'x' => $x]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Jāpiešķir lekcija - ' . Yii::$app->name)
            ->send();
    }
}
