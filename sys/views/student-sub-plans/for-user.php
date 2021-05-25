<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\SchoolSubplanParts;

$this->title = \Yii::t('app', 'Subscription plans');

?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'start_date',
            'sent_invoices_count',
            'times_paid',
            [
                'label' => Yii::t('app', 'Plan name'),
                'value' => function ($dataProvider) {
                    return $dataProvider->plan->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Plan monthly cost'),
                'value' => function ($dataProvider) {
                    return SchoolSubplanParts::getPlanTotalCost($dataProvider->plan['id']);
                }
            ],
            [
                'label' => Yii::t('app', 'Plan months count'),
                'value' => function ($dataProvider) {
                    return $dataProvider->plan->months;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a(
                            '<span><span class="glyphicon glyphicon-eye-open"> </span> Detalizēta informācija</span>',
                            $url
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    if ($action === 'view') {
                        return Url::to(['student-sub-plans/view', 'id' => $model["id"]]);
                    }
                }
            ],
        ],
    ]);
    ?>
</div>