<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = 'Rediģēt skolas iestatījumus: ';
$this->params['breadcrumbs'][] = 'Rediģēt';
?>
<div class="school-settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="school-settings-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'background_image')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>