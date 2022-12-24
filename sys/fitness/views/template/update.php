<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

['label' => \Yii::t('app',  'Templates'), 'url' => ['index']];
\Yii::t('app',  'Edit');
?>
<div class="lectures-update">
    <?= $this->render('_form') ?>

</div>
<script>
    window.templateId = <?= $templateId ?>
</script>