<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Start later commitments');

?>
<div>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'user.first_name',
            'user.last_name',
            'user.email',
            'start_date',
            [
                'attribute' => 'start_time_of_day',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['start_time_of_day']);
                }
            ],
            [
                'attribute' => 'chosen_period_started',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['chosen_period_started'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'commitment_fulfilled',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['commitment_fulfilled'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'day_before_email_sent',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['day_before_email_sent'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'half_hour_before_email_sent',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['half_hour_before_email_sent'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'missed_session_email_sent',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['missed_session_email_sent'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'week_after_missed_email_sent',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['week_after_missed_email_sent'] ? 'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'quarterly_reminder_sent',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider['quarterly_reminder_sent'] ? 'Yes' : 'No');
                }
            ],
        ],
    ]); ?>
</div>