<?php

use yii\helpers\Html;

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
<?= $this->render('lesson-list', [
    'lessons' => $newLessons,
    'currentLessonEvaluated' => $currentLessonEvaluated,
]) ?>
<?= $this->render('lesson-list', [
    'lessons' => $favouriteLessons,
    'currentLessonEvaluated' => $currentLessonEvaluated,
]) ?>
<?= Html::a(\Yii::t('app', 'Open favourite lessons'), ['?type=favourite'], ['class' => 'btn btn-gray favourite-lessons-button']) ?>