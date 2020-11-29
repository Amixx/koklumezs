<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app',  'School settings');
$this->params['breadcrumbs'][] = $this->title;
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
            <a class="nav-link" id="sqs-tab" data-toggle="tab" href="#sqs" role="tab" aria-controls="sqs" aria-selected="false"><?= \Yii::t('app', 'Student questions') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <table class="table table-striped table-bordered">
                <tr>
                    <?php foreach ($settings as $key => $setting) { ?>
                        <th>
                            <?= $key ?>
                        </th>
                    <?php } ?>
                </tr>
                <tr>
                    <?php foreach ($settings as $setting) { ?>
                        <td>
                            <?= $setting ?>
                        </td>
                    <?php } ?>
                </tr>
            </table>

            <?= Html::a(\Yii::t('app',  'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>

            <hr>
            <p>
                <strong><?= Yii::t('app', 'The link that students can use to join this school'); ?>: </strong>
                <code>https://skola.koklumezs.lv/sys/site/sign-up?s=<?= $schoolId ?>?l=<?= Yii::$app->language ?></code>
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
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
        <div class="tab-pane fade" id="sqs" role="tabpanel" aria-labelledby="sqs-tab">
            <h1><?= Yii::t("app", "Student questions") ?></h1>
             <?= GridView::widget([
                'dataProvider' => $studentQuestionsDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'student.first_name',
                    'student.last_name',
                    'student.email',
                    'text',
                ],
            ]); ?>
        </div>
    </div>    
</div>