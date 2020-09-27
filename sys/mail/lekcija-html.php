<?php

use yii\helpers\Html;

?>
<div class="password-reset">
    <p>Sveiki <?= Html::encode($userFirstName) ?>,</p>

    <p>Jums ir piešķirta jauna nodarbība - <b><?= $lectureName ?></b></p>

    <?php if (isset($teacherMessage) && $teacherMessage) { ?>
        <p><?= $teacherMessage ?></p>
    <?php } ?>
</div>