<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$evaluations = [
    [
        'value' => 2,
        'text' => "Garlaicīgi"
    ],
    [
        'value' => 4,
        'text' => "Viegli"
    ],
    [
        'value' => 6,
        'text' => "Nedaudz grūti"
    ],
    [
        'value' => 8,
        'text' => "Ļoti grūti"
    ],
    [
        'value' => 10,
        'text' => "Neiespējami"
    ],
];

$isButtonACtive = function ($name) use ($evaluations, $difficultyEvaluation) {
    if (!$difficultyEvaluation) return false;

    $evalIndex = array_search($name, array_column($evaluations, 'text'));

    return $evaluations[$evalIndex]['value'] === intval($difficultyEvaluation->evaluation);
};

?>

<p style="font-size: 18px; <?= isset($readonly) && $readonly ? '' : 'font-weight: bold' ?>">Kā gāja?</p>
<div>
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::hiddenInput("difficulty-evaluation", null) ?>

    <div class="btn-group">
        <?php foreach ($evaluations as $evaluation) {
            $name = $evaluation['text'];
            $emojiClass = "emoji emoji-$name";
            ?>
            <button
                data-role="evaluation-emoji"
                data-value="<?= $evaluation['value'] ?>"
                class="btn <?= $isButtonACtive($name) ? 'btn-primary' : '' ?>"
                <?= isset($readonly) && $readonly ? 'disabled' : '' ?>
            >
                <?= $evaluation['text'] ?>
            </button>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>