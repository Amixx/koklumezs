<?php

use yii\helpers\Html;

?>
<div class="password-reset">
    <p>Sveiki,</p>

    <p>Tev ir piešķirta jauna nodarbība!</p>

    <?php if (isset($teacherMessage) && $teacherMessage) { ?>
        <p><?= $teacherMessage ?></p>
    <?php } ?>
</div>