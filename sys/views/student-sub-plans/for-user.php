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
                    return $dataProvider->plan ? $dataProvider->plan->name : "-";
                }
            ],
            [
                'label' => Yii::t('app', 'Plan monthly cost'),
                'value' => function ($dataProvider) {
                    return $dataProvider->plan
                        ? SchoolSubplanParts::getPlanTotalCost($dataProvider->plan['id'])
                        : "-";
                }
            ],
            [
                'label' => Yii::t('app', 'Plan end date'),
                'value' => function ($dataProvider) use ($planEndDates) {
                    foreach ($planEndDates as $planEndDate) {
                        if ($planEndDate['planId'] == $dataProvider->id) {
                            return $planEndDate['endDate'];
                        }
                    }
                    return NULL;
                },
                'format' => 'html'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a(
                            '<span style="display:inline-block"><span class="glyphicon glyphicon-eye-open"> </span> Plāna faili</span>',
                            $url
                        );
                    },
                    'update' => function ($url, $provider) {
                        if ($provider['plan']['type'] === 'rent') {
                            return null;
                        }

                        return Html::a(
                            '<span style="display:inline-block"><span class="glyphicon glyphicon-pencil"> </span> Nopauzēt plānu</span>',
                            $url
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    if ($action === 'view') {
                        return Url::to(['student-sub-plans/view', 'id' => $model["id"]]);
                    }
                    if ($action === 'update') {
                        return Url::to(['student-sub-plans/pause', 'id' => $model["id"]]);
                    }
                }
            ],
        ],
    ]);
    ?>
</div>