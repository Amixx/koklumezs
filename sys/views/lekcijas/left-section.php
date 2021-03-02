<?php

use yii\helpers\Url;
use yii\helpers\Html;

if (isset($sortByDifficulty) && ($sortByDifficulty == 'desc')) {
    $sortByDifficultyLabel = 'From easiest to hardest';
} else {
    $sortByDifficultyLabel = 'From hardest to easiest';
}
?>

<h3 class="text-center"><?=\Yii::t('app',  'New lessons')?></h3>
<?php if (count($userLectures) > 1) { ?>
    <?= Html::a(\Yii::t('app', $sortByDifficultyLabel), '?sortByDifficulty='.$sortByDifficulty,['class' => 'btn btn-gray sort-button']) ?>
<?php } ?>
<?php foreach ($userLectures as $lecture) {  ?>
    <?php if ($lecture->sent) { ?>
        <p>
            <a
                class="lesson-column-item"
                href="<?= Url::to(['lekcijas/lekcija','id' => $lecture->lecture_id]); ?>"
            ><?= $lecture->lecture->title ?></a>
        </p>
    <?php } ?>
<?php } ?>
<?= Html::a(\Yii::t('app', 'Open favourite lessons'), ['?type=favourite'], ['class' => 'btn btn-gray favourite-lessons-button']) ?>