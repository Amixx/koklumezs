<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Messages after evaluation');

?>
<div class="settings-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php for ($i = 2; $i <= 10; $i += 2) { ?>
        <?= $this->render('message-table', [
            'messages' => $messages[$i],
            'evaluation' => $i
        ]) ?>
    <?php } ?>

</div>