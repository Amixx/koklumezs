<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = \Yii::t('app',  'Create category');
 ['label' => \Yii::t('app',  'Categories'), 'url' => ['index']];

?>
<div class="handdifficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>