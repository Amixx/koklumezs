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

$isEmojiActive = function($name) use($evaluations, $difficultyEvaluation) {
    if(!$difficultyEvaluation) return false;

    $evalIndex = array_search($name, array_column($evaluations, 'emoji-name'));
    
    return $evaluations[$evalIndex]['value'] === intval($difficultyEvaluation->evaluation);
};

?>

<p>Hei! Kā tev veicās ar šo uzdevumu?</p>
<div>
    <?php $form = ActiveForm::begin(); ?>
    <?php if (!$force) { ?>
        <?= Html::hiddenInput("difficulty-evaluation", null) ?>
        <?php if($redirectToNext){
            echo Html::hiddenInput('redirect-to-next', true);
        } ?>
        <div class="form-group">
            <?php foreach($evaluations as $evaluation){
                $name = $evaluation['emoji-name'];
                $emojiClass = "emoji emoji-$name";
                if($isEmojiActive($name)) $emojiClass .= " active";            
            ?>

                <span
                    data-role="evaluation-emoji"
                    data-value="<?= $evaluation['value'] ?>"
                    class="<?= $emojiClass ?>"
                ></span>
            <?php } ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>            
</div>       