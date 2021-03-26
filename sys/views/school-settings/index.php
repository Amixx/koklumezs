<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app',  'School settings');

?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app', 'Settings') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false"><?= \Yii::t('app', 'Parameters') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="faqs-tab" data-toggle="tab" href="#faqs" role="tab" aria-controls="faqs" aria-selected="false"><?= \Yii::t('app', 'FAQs') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="signup-questions-tab" data-toggle="tab" href="#signup-questions" role="tab" aria-controls="signup-questions" aria-selected="false"><?= \Yii::t('app', 'Questions after signup') ?></a>
        </li>
        <li class="nav-item">
            <?= Html::a(\Yii::t('app', 'Registration lessons'), ['/registration-lessons'], ['class' => 'nav-link']) ?>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <table class="table table-striped table-bordered">
                <?php foreach ($settings as $key => $value) { ?>
                    <tr>
                        <th><?= $key ?></th>
                        <td><?= $value ?></td>
                    </tr>
                <?php } ?>
            </table>

            <?= Html::a(\Yii::t('app',  'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>

            <hr>
            <p>
                <strong><?= Yii::t('app', 'The link that students can use to join this school'); ?>: </strong>
                <code><?= $signupUrl ?></code>
            </p>
            <p>
                <strong><?= Yii::t('app', 'The link that students can use to log in this school'); ?>: </strong>
                <code><?= $loginUrl ?></code>
            </p>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?= $this->render("difficulties", [
                'dataProvider' => $difficultiesDataProvider
            ]) ?>
        </div>
        <div class="tab-pane fade" id="faqs" role="tabpanel" aria-labelledby="faqs-tab">
            <h1><?= Yii::t("app", "FAQs") ?></h1>

            <p>
                <?= Html::a(\Yii::t('app', 'Create a FAQ'), ['/school-faqs/create'], ['class' => 'btn btn-success']) ?>
            </p>
            <?= GridView::widget([
                'dataProvider' => $faqsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'question',
                    'answer',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    $url,
                                    ['title' => Yii::t('app', 'Update')]
                                );
                            },
                        ],
                        'urlCreator' => function ($action, $model) {
                            if ($action === 'update') {
                                $url = '/school-faqs/update?id=' . $model->id;
                                return $url;
                            }
                            if ($action === 'delete') {
                                $url = '/school-faqs/delete?id=' . $model->id;
                                return $url;
                            }
                        },
                    ],
                ],
            ]); ?>
        </div>
        <div class="tab-pane fade" id="signup-questions" role="tabpanel" aria-labelledby="signup-questions-tab">
            <h1><?= Yii::t("app", "Questions after signup") ?></h1>
            <?= GridView::widget([
                'dataProvider' => $signupQuestionsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'text',
                ],
            ]); ?>
            <hr>
            <h3><?= Yii::t('app', 'Add a new question') ?></h3>
            <?php $form = ActiveForm::begin([
                'action' => ['signup-questions/create'],
                'method' => 'post',
            ]); ?>
            <label for="new-question-text">
                <?= Yii::t('app', 'Question text') ?>:
            </label>
            <?= Html::textarea('new-question-text', '', ['class' => 'signup-questions-textarea']) ?>
            <div class="form-group">
                <?= Html::submitButton(\Yii::t('app',  'Submit'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>