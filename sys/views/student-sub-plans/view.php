<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use app\models\StudentSubPlans;

if ($subplan) {
    $this->title = $subplan['plan']['name'];
}
\yii\web\YiiAsset::register($this);
?>
<div class="difficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($subplan) { ?>

        <?= DetailView::widget([
            'model' => $subplan,
            'attributes' => [
                'start_date',
                [
                    'attribute' => 'Plan end date',
                    'label' => Yii::t('app', 'Plan end date'),
                    'value' => function ($dataProvider) {
                        return StudentSubPlans::getEndDateString($dataProvider['user_id']);
                    },
                    'format' => 'raw'
                ],
                'sent_invoices_count',
                'plan.name',
            ],
        ]) ?>

        <h3><?= Yii::t('app', 'Plan files') ?></h3>
        <?php foreach ($planFiles as $file) { ?>
            <p>
                <a href="<?= $file['file'] ?>" target="_blank"><?= $file['title'] ?></a>
            </p>
        <?php } ?>


        <?php if ($planPauses) {
            echo "<h3>" . Yii::t('app', 'Plan pauses') . "</h3>";
            echo GridView::widget([
                'dataProvider' => $planPauses,
                'columns' => [
                    'weeks',
                    'start_date',
                ],
            ]);
        } ?>
        <hr>
        <?php if (!$planCurrentlyPaused) { ?>
            <?php if ($remainingPauseWeeks > 0) { ?>
                <h3><?= Yii::t('app', 'Pause the plan') ?></h3>
                <p>
                    <?= Yii::t('app', 'You have to do monthly payments for the pause weeks too. At the end of subscribtion plan all pauses will be summed up and the plan will be extended with free lessons.') ?>
                </p>
                <?php $form = ActiveForm::begin([
                    'action' => ['student-subplan-pauses/create'],
                    'method' => 'post',
                ]); ?>

                <div class="form-group">
                    <label class="control-label" for="studentsubplanpauses-weeks">
                        <?= Yii::t('app', 'For how long? (Max: {0} weeks)', [$remainingPauseWeeks]) ?>
                    </label>
                    <input type="number" style="width:75px;" max="<?= $remainingPauseWeeks ?>" min="1" id="studentsubplanpauses-weeks" class="form-control" name="StudentSubplanPauses[weeks]">
                </div>
                <?= $form->field($newPause, 'start_date')
                    ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])
                    ->label(Yii::t('app', 'I will start the pause by this date:')) ?>
                <?= $form->field($newPause, 'studentsubplan_id')
                    ->hiddenInput(['value' => $subplan['id']])
                    ->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-orange']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            <?php } else { ?>
                <h4><?= Yii::t('app', 'You can not pause the plan') ?>.</h4>
                <p><?= Yii::t('app', 'You have used the number of pause weeks specified in the plan') ?>.</p>
            <?php } ?>
        <?php } else { ?>
            <h4><?= Yii::t('app', 'Plan is currently paused') ?>!</h4>
        <?php } ?>

    <?php } else { ?>
        <h3>Tev šobrīd nav piešķirts abonēšanas plāns!</h3>
    <?php } ?>
</div>