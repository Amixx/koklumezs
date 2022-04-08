<?php

use yii\helpers\Html;

if (isset($sortType)) {
    if ($sortType == 0) {
        $sortTypeLabel = 'From hardest to easiest';
    } else if ($sortType == 1) {
        $sortTypeLabel = 'From easiest to hardest';
    } else {
        $sortTypeLabel = 'By assignment date';
    }
}

$titleText = $isFitnessSchool ? 'Other exercises in this workout' : 'New lessons';

?>

<h3 class="text-center"><?= \Yii::t('app',  $titleText) ?></h3>

<?php if (!empty($newLessons)) { ?>
    <?= Html::a(
        \Yii::t('app', $sortTypeLabel),
        '?sortType=' . $sortType,
        ['class' => 'btn btn-gray sort-button', 'style' => 'padding: 4px; margin-left: 0; width: 100%;']
    ) ?>
<?php } ?>
<?= $this->render('lesson-list', [
    'userLessons' => $newLessons,
    'currentLessonEvaluated' => $currentLessonEvaluated,
]) ?>
<?= Html::a(
    \Yii::t('app', 'Open favourite lessons'),
    ['?type=favourite'],
    ['class' => 'btn btn-gray favourite-lessons-button']
) ?>