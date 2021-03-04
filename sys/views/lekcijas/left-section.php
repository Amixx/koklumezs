<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if (isset($sortByDifficulty) && ($sortByDifficulty == 'desc')) {
    $sortByDifficultyLabel = 'From easiest to hardest';
} else {
    $sortByDifficultyLabel = 'From hardest to easiest';
}
?>

<h3 class="text-center"><?=\Yii::t('app',  'New lessons')?></h3>
<?php if (!empty($userLectures)) { ?>
    <?= Html::a(\Yii::t('app', $sortByDifficultyLabel), '?sortByDifficulty='.$sortByDifficulty,['class' => 'btn btn-gray sort-button']) ?>
<?php } ?>
<?php foreach ($userLectures as $lecture) {  ?>
    <?php if ($lecture->sent) { ?>
        <?php if($currentLessonEvaluated) { ?>
        <p>
            <a
                class="lesson-column-item"
                href="<?= Url::to(['lekcijas/lekcija','id' => $lecture->lecture_id]); ?>"
            ><?= $lecture->lecture->title ?></a>
        </p>            
        <?php } else { ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= Html::hiddenInput("difficulty-evaluation", null) ?>
            <?php ActiveForm::end(); ?>

            <?php
            $modalType = "lesson-" . str_replace( ' ', '_', $lecture->lecture->title);
            echo $this->render('alertEvaluation', [
                'idPostfix' => $modalType,
                'force' => false,
                'difficultyEvaluation' => null,
                'redirectLessonId' => $lecture->lecture->id,
            ]);
            ?>

            <a class="lesson-column-item" data-toggle="modal" data-target="#alertEvaluation-<?= $modalType ?>"><?= $lecture->lecture->title ?></a>
        <?php } ?>    
    <?php } ?>
<?php } ?>
<?= Html::a(\Yii::t('app', 'Open favourite lessons'), ['?type=favourite'], ['class' => 'btn btn-gray favourite-lessons-button']) ?>