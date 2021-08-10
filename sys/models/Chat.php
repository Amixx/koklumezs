<?php

namespace app\models;

use Yii;

class Chat extends \yii\db\ActiveRecord
{

    public $userModel;

    public static function tableName()
    {
        return 'chat';
    }

    public function rules()
    {
        return [
            [['message'], 'required'],
            [['author_id', 'recipient_id', 'status'], 'integer'],
            [['update_date', 'message', 'status'], 'safe']
        ];
    }

    public function getAuthor()
    {
        if (isset($this->userModel)) {
            return $this->hasOne($this->userModel, ['id' => 'author_id']);
        } else {
            return $this->hasOne(Yii::$app->getUser()->identityClass, ['id' => 'author_id']);
        }
    }

    public function getRecipient()
    {
        if (isset($this->userModel)) {
            return $this->hasOne($this->userModel, ['id' => 'recipient_id']);
        } else {
            return $this->hasOne(Yii::$app->getUser()->identityClass, ['id' => 'recipient_id']);
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'author_id' => 'Author',
            'recipient_id' => 'Recipient',
            'update_date' => 'Update Date',
        ];
    }

    public static function recordsForTwoUsers($authorId, $recipientId, $isTeacher)
    {

        $chatMessages = static::find()->andWhere([
            'or',
            ['author_id' => $authorId, 'recipient_id' => $recipientId],
            ['author_id' => $recipientId, 'recipient_id' => $authorId],
        ])->orderBy('id asc')->all();

        $needHelpMessagesAuthorId = $isTeacher ? $recipientId : $authorId;
        $needHelpMessages = NeedHelpMessages::getFormattedForChat($needHelpMessagesAuthorId);

        $allMessages = array_merge($chatMessages, $needHelpMessages);

        usort($allMessages, function ($a, $b) {
            return strtotime($a->update_date) - strtotime($b->update_date);
        });

        return $allMessages;
    }

    public static function getUnreadCountInCorrespondence($authorId, $recipientId)
    {
        $opentime = CorrespondenceOpentimes::getOpentimeValue($authorId, $recipientId);
        if (!$opentime) {
            $opentime = Yii::$app->user->identity['last_opened_chat'];
        }

        $chatMessagesQuery = static::find()
            ->where([
                'recipient_id' => $authorId,
                'author_id' => $recipientId
            ]);

        $needHelpMessagesQuery = NeedHelpMessages::find()
            ->where(['author_id' => $recipientId]);

        if ($opentime) {
            $chatMessagesQuery->andWhere(['>', 'update_date', $opentime]);
            $needHelpMessagesQuery->andWhere(['>', 'created_at', $opentime]);
        }

        $unseenChatMessagesCount = (int) $chatMessagesQuery->count();
        $unseenNeedHelpMessagesCount = (int) $needHelpMessagesQuery->count();

        return $unseenChatMessagesCount + $unseenNeedHelpMessagesCount;
    }

    public static function unreadCountForCurrentUser()
    {
        $currentUserId = Yii::$app->user->identity->id;

        $totalUnreadCount = 0;
        $usersWithConversations = self::getUsersWithConversations($currentUserId);
        foreach ($usersWithConversations as $user) {
            $totalUnreadCount += self::getUnreadCountInCorrespondence($currentUserId, $user['id']);
        }

        return $totalUnreadCount;
    }

    public static function hasNewChats($senderId)
    {
        $currentUserId = Yii::$app->user->identity->id;

        return self::getUnreadCountInCorrespondence($currentUserId, $senderId);
    }

    public static function getUsersWithConversations($authorId)
    {
        $userIdsData = static::find()
            ->select(['author_id', 'recipient_id'])
            ->andWhere([
                'or',
                ['author_id' => $authorId],
                ['recipient_id' => $authorId],
            ])
            ->orderBy('id desc')
            ->asArray()
            ->all();

        $userIds = [];
        foreach ($userIdsData as $data) {
            if (!in_array($data['author_id'], $userIds) && $data['author_id'] != $authorId) {
                $userIds[] = $data['author_id'];
            }
            if (!in_array($data['recipient_id'], $userIds) && $data['recipient_id'] != $authorId) {
                $userIds[] = $data['recipient_id'];
            }
        }

        $users = Users::find()->where(["in", "id", $userIds])->asArray()->all();
        $usersByIds = array_column($users, NULL, 'id');

        return array_map(function ($id) use ($usersByIds) {
            if (isset($usersByIds[$id])) {
                return $usersByIds[$id];
            }
        }, $userIds);
    }

    public static function findFirstRecipient()
    {
        $authorId = Yii::$app->user->identity->id;
        $data = static::find()->where(['!=', 'recipient_id', $authorId])->andWhere([
            'or',
            ['author_id' => $authorId],
            ['recipient_id' => $authorId],
        ])->orderBy('id desc')
            ->limit(1)
            ->one();

        if ($data) {
            return $data['recipient_id'];
        } else {
            return null;
        }
    }

    public static function addNewMessage($message, $authorId, $recipientId, $status)
    {
        $model = new Chat;

        $model->message = $message;
        $model->author_id = $authorId;
        $model->status = $status;
        $model->recipient_id = $recipientId;

        return $model->save();
    }

    public function data($recipientId, $updateOpentime = true)
    {
        $output = "";
        $userList = null;
        $currentUserId = Yii::$app->user->identity->id;
        $isTeacher = Users::isCurrentUserTeacher();
        $messages = Chat::recordsForTwoUsers($currentUserId, $recipientId, $isTeacher);
        $usersWithConversations = $isTeacher ? Chat::getUsersWithConversations($currentUserId) : null;

        if ($updateOpentime) {
            CorrespondenceOpentimes::updateOpentime($currentUserId, $recipientId);
        }

        if ($messages) {
            foreach ($messages as $message) {
                $isNeedHelpMessage = property_exists($message, 'is_need_help_message');
                $outerClass = "chat-message";
                $needHelpPrefix = "";

                if ($isNeedHelpMessage) {
                    $outerClass .= " $outerClass--need-help";
                    $lessonTitle = $message->lesson->title;
                    $needHelpPrefix = "<p class='chat-message-help-prefix'>Vajadzīga palīdzība ar nodarbību <u>$lessonTitle</u>...</p>";
                }


                $output .= '<div class="' . $outerClass . '">
                    ' . $needHelpPrefix . ' 
                    <p class="message">
                        <a class="name" href="#">
                            <small class="text-muted pull-right" style="color:green"><i class="fa fa-clock-o"></i> ' . $message->update_date . '</small>
                            ' . $message->author->first_name . ' ' . $message->author->last_name . '                        
                        </a>
                    ' . $message->message . '
                    </p>
                </div>';
            }
        }


        if ($usersWithConversations) {
            usort($usersWithConversations, function ($userA, $userB) {
                if ($userA == NULL || $userB == NULL) {
                    return 0;
                }

                $aHasNew = Chat::hasNewChats($userA['id']);
                $bHasNew = Chat::hasNewChats($userB['id']);

                if ($aHasNew < $bHasNew) return 1;
                if ($aHasNew > $bHasNew) return -1;

                return 0;
            });

            foreach ($usersWithConversations as $user) {
                if ($user == NULL) {
                    continue;
                }

                $isActive = $user['id'] == $recipientId;
                $style = $isActive ? "background-color:#b0f3fc;" : "";
                $hasNewChats = Chat::hasNewChats($user['id']);
                if ($hasNewChats) {
                    $style .= "font-weight: bold;";
                }

                $userList .= "
                  <li class='chat-user-item' data-userid='" . $user['id'] . "' style='" . $style . "'>
                    <span class='glyphicon glyphicon-user'></span>
                    <span>" . $user['first_name'] . " " . $user['last_name'] . "</span>
                  </li>
                ";
            }
        }

        return [
            'content' => $output,
            'userList' => $userList
        ];
    }

    public static function isChatCooldown()
    {
        $userId = Yii::$app->user->id;
        $lastSystemMessage = self::find()->where(['recipient_id' => $userId, 'status' => 2])->orderBy('id desc')->limit(1)->all();
        $lastMessageTime = strtotime($lastSystemMessage[0]->update_date);
        $now = time();
        $isCooldown = !(round(($now - $lastMessageTime) / 60, 2) >= 10);

        return $isCooldown;
    }
}
