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
            [['created', 'opentime', 'sent'], 'safe'],
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
    public function getUserLectures($id): array
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id, 'evaluated' => 0])->asArray()->all(), 'id', 'lecture_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluatedLectures($id): array
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id, 'evaluated' => 1])->asArray()->all(), 'id', 'lecture_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLectureTimes($id): array
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id])->asArray()->all(), 'lecture_id', 'created');
    }

    public function getLastLecturesForUser($id, $limit = 5)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->asArray()->all(), 'id', 'lecture_id');
    }

    public function getLectures($id, $evaluated = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated])->orderBy(['lecture_id' => SORT_ASC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function setSeenByUser($user_id, $id)
    {
        if (($user = Users::findOne($user_id)) !== null) {
            $model = self::find()->where(['opened' => 0, 'user_id' => $user_id, 'lecture_id' => $id])->one();
            if ($model) {
                $model->opened = 1;
                $model->opentime = date('Y-m-d H:i:s', time());
                $model->update();
            }
            $user->last_lecture = $id;
            $user->update();
        }
        return true;
    }

    public function getOpened($id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id, 'opened' => 1])->asArray()->all(), 'lecture_id', 'id');
    }

    public function getNewLectures($user_id, $ids = []): array
    {
        $results = [];
        $current = ArrayHelper::map(self::find()->where(['user_id' => $user_id])->orderBy(['lecture_id' => SORT_ASC])->all(), 'lecture_id', 'id');
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
            ->setSubject('Jauna lekcija ' . Yii::$app->name)
            ->send();
    }
}
