<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$evaluations = [
    [
        'value' => 2,
        'emoji-name' => "sleepy"
    ],
    [
        'value' => 4,
        'emoji-name' => "wink"
    ],
    [
        'value' => 6,
        'emoji-name' => "smile"
    ],
    [
        'value' => 8,
        'emoji-name' => "surprise"
    ],
    [
        'value' => 10,
        'emoji-name' => "fatigue"
    ],
];

$isEmojiActive = function ($name) use ($evaluations, $difficultyEvaluation) {
    if (!$difficultyEvaluation) return false;

    $evalIndex = array_search($name, array_column($evaluations, 'emoji-name'));

    return $evaluations[$evalIndex]['value'] === intval($difficultyEvaluation->evaluation);
};

?>

<p><?= \Yii::t('app', 'How well did you do with this task?'); ?></p>
<div>
    <?php $form = ActiveForm::begin(); ?>
    <?php if (!$force) { ?>
        <?= Html::hiddenInput("difficulty-evaluation", null) ?>
        <?php if ($redirectLessonId) {
            echo Html::hiddenInput('redirect-lesson-id', $redirectLessonId);
        } ?>
        <div class="form-group" style="margin: 0;">
            <?php foreach ($evaluations as $evaluation) {
                $name = $evaluation['emoji-name'];
                $emojiClass = "emoji emoji-$name";
                if ($isEmojiActive($name)) $emojiClass .= " active";
            ?>

                <span data-role="evaluation-emoji" data-value="<?= $evaluation['value'] ?>" class="<?= $emojiClass ?>"></span>
            <?php } ?>
        </div>
    <?php } ?>
    <p>
        <span style="padding-left: 10px;"><?= \Yii::t('app', 'easy'); ?></span>
        <span class="glyphicon glyphicon-arrow-left" style="padding-left: 8px;"></span>
        <span class="glyphicon glyphicon-minus"></span>
        <span class="glyphicon glyphicon-minus"></span>
        <span class="glyphicon glyphicon-minus"></span>
        <span class="glyphicon glyphicon-arrow-right"></span>
        <span style="padding: 5px;"><?= \Yii::t('app', 'hard'); ?></span>
    </p>
    <?php ActiveForm::end(); ?>
</div>