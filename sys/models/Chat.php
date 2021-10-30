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
            return $this->hasOne(Users::class, ['id' => 'author_id']);
        }
    }

    public function getRecipient()
    {
        if (isset($this->userModel)) {
            return $this->hasOne($this->userModel, ['id' => 'recipient_id']);
        } else {
            return $this->hasOne(Users::class, ['id' => 'recipient_id']);
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

    // TODO: iespējams šis arī jāpārnes uz user modeli
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

    public static function addNewMessage($message, $authorId, $recipientId, $status = 1)
    {
        // 1 - User message
        // 2 - System message (currently only used for after evaluation messages)
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
        $userContext = Yii::$app->user->identity;
        $currentUserId = $userContext->id;
        $isTeacher = $userContext->isTeacher();
        $messages = Chat::recordsForTwoUsers($currentUserId, $recipientId, $isTeacher);
        $user = Users::getCurrentUserForChat();

        $usersWithConversations = $isTeacher ? $user->getUsersWithConversations() : null;

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
            foreach ($usersWithConversations as $u) {
                if ($u == NULL) {
                    continue;
                }

                $isActive = $u['id'] == $recipientId;
                $style = $isActive ? "background-color:#b0f3fc;" : "";
                $hasNewChats = $user->hasUnreadMessages($u['id']);
                if ($hasNewChats) {
                    $style .= "font-weight: bold;";
                }

                $userList .= "
                  <li class='chat-user-item' data-userid='" . $u['id'] . "' style='" . $style . "'>
                    <span class='glyphicon glyphicon-user'></span>
                    <span>" . $u['first_name'] . " " . $u['last_name'] . "</span>
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
        $lastSystemMessage = self::find()->where(['recipient_id' => $userId, 'status' => 2])->orderBy('id desc')->one();
        if (!$lastSystemMessage) {
            return false;
        }

        $lastMessageTime = strtotime($lastSystemMessage->update_date);
        $now = time();
        $isCooldown = !(round(($now - $lastMessageTime) / 60, 2) >= 10);

        return $isCooldown;
    }
}
