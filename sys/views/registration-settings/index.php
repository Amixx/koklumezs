<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app',  'Registration settings');

?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link" id="signup-questions-tab" data-toggle="tab" href="#signup-questions" role="tab" aria-controls="signup-questions" aria-selected="false">
                <?= Yii::t('app', 'Questions after signup') ?>
            </a>
        </li>
        <li class="nav-item">
            <?= Html::a(
                Yii::t('app', 'Registration lessons and messages'),
                ['/registration-lessons'],
                ['class' => 'nav-link']
            ) ?>
        </li>
        <li class="nav-item">
            <?= Html::a(
                Yii::t('app', 'Registration e-mails'),
                ['/school-registration-emails'],
                ['class' => 'nav-link']
            ) ?>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="signup-questions" role="tabpanel" aria-labelledby="signup-questions-tab">
            <h1><?= Yii::t("app", "Questions after signup") ?></h1>
            <?= GridView::widget([
                'dataProvider' => $signupQuestionsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'text',
                    [
                        'attribute' => 'multiple_choice',
                        'value' => function ($dataProvider) {
                            $word = $dataProvider['multiple_choice'] ? "Yes" : "No";

                            return Yii::t('app', $word);
                        }
                    ],
                    [
                        'attribute' => 'answer_choices',
                        'label' => Yii::t('app', 'Answer choices'),
                        'format' => 'html',
                        'value' => function ($dataProvider) {
                            if (!$dataProvider->answerChoices || empty($dataProvider->answerChoices) || !$dataProvider->multiple_choice) return "<em>Nav</em>";

                            $res = "<ul>";

                            foreach ($dataProvider->answerChoices as $choice) {
                                $text = $choice['text'];
                                $res .= "<li>$text</li>";
                            }

                            $res .= "</ul>";
                            return $res;
                        }
                    ],
                    [
                        'attribute' => 'allow_custom_answer',
                        'value' => function ($dataProvider) {
                            $word = $dataProvider['multiple_choice'] ? "Yes" : "No";

                            return Yii::t('app', $word);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"> </span>',
                                    $url,
                                    ['title' => 'Delete', 'data-pjax' => '0', 'data-method' => 'POST']
                                );
                            },
                        ],
                        'urlCreator' => function ($action, $model) {
                            if ($action === 'delete') {
                                return Url::base(true) . '/signup-questions/delete?id=' . $model['id'];
                            }
                        }
                    ],
                ],
            ]); ?>
            <hr>
            <?php $form = ActiveForm::begin([
                'action' => ['signup-questions/create'],
                'method' => 'post',
            ]); ?>
            <?= Html::a(Yii::t('app', 'Add a new question'), ['signup-questions/create'], ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>