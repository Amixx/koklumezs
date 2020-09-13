<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = \Yii::t('app',  'Edit school settings') . ': ';
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');
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
        <?= $form->field($model, 'video_thumbnail')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>