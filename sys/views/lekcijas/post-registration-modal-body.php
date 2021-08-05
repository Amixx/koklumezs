<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use  yii\jui\DatePicker;
use yii\helpers\Html;

?>

<div>
    <div id="post-registration-modal-buttons">
        <a class="btn btn-orange" id="btn-start-instantly" href="<?= Url::to(['user/start-now']) ?>">Esmu gatavs spēlēt jau tagad, dodiet tik uzdevumus!</a>
        <button class="btn btn-blue" id="btn-start-later">Vēlēšos sākt nedaudz vēlāk</button>
    </div>

    <?php $form = ActiveForm::begin([
        'action' => '/user/start-later',
        'options' => [
            'id' => 'start-later-form'
        ]
    ]); ?>
    <?= $form->field($model, 'start_date')
        ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])
        ->label(Yii::t('app', 'I\'ll start on this date') . ': ') ?>

    <?= $form->field($model, 'start_time_of_day')->radio([
        'label' => Yii::t('app', 'In the morning') . ' (8:00 - 13:00)',
        'value' => 'morning',
        'uncheck' => null
    ]) ?>
    <?= $form->field($model, 'start_time_of_day')->radio([
        'label' => Yii::t('app', 'In the afternoon') . ' (13:00 - 17:00)',
        'value' => 'afternoon',
        'uncheck' => null
    ]) ?>
    <?= $form->field($model, 'start_time_of_day')->radio([
        'label' => Yii::t('app', 'In the evening') . ' (17:00 - 23:00)',
        'value' => 'evening',
        'uncheck' => null
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>