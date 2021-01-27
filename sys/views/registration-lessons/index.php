<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Lessons to assign after registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3><?= Yii::t('app', 'Lessons for students without experience') ?></h3>

                 <?= GridView::widget([
                    'dataProvider' => $withoutExperience,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'lesson.title',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}'
                        ],
                    ],
                ]); ?>
            </div>
            <div class="col-sm-6">
                <h3><?= Yii::t('app', 'Lessons for students with experience') ?></h3>

                 <?= GridView::widget([
                    'dataProvider' => $withExperience,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'lesson.title',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}'
                        ],
                    ],
                ]); ?>                
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'lesson_id')->dropDownList($lectures, ['prompt' => '', 'type' => 'number']) ?>

                <?= $form->field($model, 'for_students_with_experience')->dropDownList([0 => 'bez pieredzes', 1 => 'ar pieredzi'], ['prompt' => '', 'type' => 'number'])->label('Kuriem skolēniem piešķirt nodarbību') ?>

                <div class="form-group">
                    <?= Html::submitButton(\Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

   


</div>