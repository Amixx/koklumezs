<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<h3 class="text-center"><?=\Yii::t('app',  'New lessons')?></h3>
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
<?= Html::a(\Yii::t('app', 'Open favourite lessons'), ['?type=favourite'], ['class' => 'btn favourite-lessons-button']) ?>