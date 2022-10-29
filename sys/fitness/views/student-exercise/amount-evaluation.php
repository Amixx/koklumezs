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

$isButtonActive = function ($name) use ($evaluations, $difficultyEvaluation) {
    if (!$difficultyEvaluation) return false;

    $evalIndex = array_search($name, array_column($evaluations, 'text'));

    return $evaluations[$evalIndex]['value'] === intval($difficultyEvaluation->evaluation);
};

?>

<p style="font-size: 18px; <?= isset($readonly) && $readonly ? '' : 'font-weight: bold' ?>">Kā gāja?</p>
<div>
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::hiddenInput("difficulty-evaluation", null) ?>

    <div class="fitness-difficulty-eval">
        <div class="btn-group">
            <?php foreach ($evaluations as $evaluation) {
                $name = $evaluation['text'];
                $emojiClass = "emoji emoji-$name";
                ?>
                <button
                        type="button"
                        data-role="fitness-eval-btn"
                        data-value="<?= $evaluation['value'] ?>"
                        class="btn <?= $isButtonActive($name) ? 'active' : '' ?>"
                        title="<?= $evaluation['text'] ?>"
                    <?= isset($readonly) && $readonly ? 'disabled' : '' ?>
                >
                    &nbsp;
                </button>
            <?php } ?>
        </div>
        <div class="fitness-difficulty-eval__description">
            <span>Pārāk viegli</span>
            <span>Ideāli</span>
            <span>Pārāk grūti</span>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
</div>