<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\elfinder\InputFile;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\grid\GridView;

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);
?>

<div class="difficulties-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app', 'Plan') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false"><?= \Yii::t('app', 'Plan files') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'description')->widget(CKEditor::className(), [
                'editorOptions' => $ckeditorOptions,
            ]) ?>
            <?= $form->field($model, 'monthly_cost')->textInput() ?>
            <?= $form->field($model, 'months')->textInput() ?>
            <?= $form->field($model, 'max_pause_weeks')->textInput() ?>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if(isset($planFiles)){
                echo GridView::widget([
                    'dataProvider' => $planFiles,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'title',
                        'file',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}',
                            'buttons' => [
                                'delete' => function ($url, $model) {
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-trash"> </span>',
                                        $url,
                                        ['title' => 'Delete', 'data-method' => 'post']
                                    );
                                },
                            ],
                            'urlCreator' => function ($action, $model) {
                                if ($action === 'delete') {
                                    $url = '/sys/plan-files/delete?id='.$model["id"];
                                    return $url;
                                }
                            }
                        ],
                    ],
                ]);
            } ?>
            <label for="file-title">
                <?= Yii::t('app', 'File title') ?>
                <input type="text" name="file-title" class="form-control">
            </label>
            <label for="file">
                <?= Yii::t('app', 'File') ?>
                <?= InputFile::widget([
                    'name' => 'file',
                    'language' => 'lv',
                    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                    'options' => ['class' => 'form-control'],
                    'buttonOptions' => ['class' => 'btn btn-default'],
                    'multiple' => false, // возможность выбора нескольких файлов
                ]); ?>
            </label>
        </div>
        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>