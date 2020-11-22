<?php

use yii\helpers\Html;

?>
<div class="password-reset">
    <p>Sveiki,</p>

    <p>Tev ir piešķirta jauna nodarbība!</p>

    <?php if (isset($teacherMessage) && $teacherMessage) { ?>
        <p><?= $teacherMessage ?></p>
    <?php } ?>

    <p><a href="https://skola.koklumezs.lv/" target="_blank">Iet uz skolu</a></p>
</div>