<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\StudentSubplanPauses;
use app\models\dataProvider;
use  yii\jui\DatePicker;

$this->title = \Yii::t('app',  'Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div>
    <label for="sent-invoices-filter"><?= Yii::t("app", "Search") ?>&nbsp;(<?= Yii::t("app", "enter at least 4 symbols") ?>): 
        <input type="text" name="sent-invoices-filter" class="form-control">
    </label>
    <label for="invoices-year-selector">Meklēt pēc datuma. Jāizvēlās gan gadu, gan mēnesi: 
    </label>
    <?= Html::dropDownList('year', null, [
        2020 => "2020",
        2021 => "2021",
        2022 => "2022",
        2023 => "2023",
    ], ['prompt' => Yii::t('app', 'Choose year'), 'id' => 'invoices-year-selector']) ?>
    <?= Html::dropDownList('month', null, [
        "Janvāris",
        "Februāris",
        "Marts",
        "Aprīlis",
        "Maijs",
        "Jūnijs",
        "Jūlijs",
        "Augusts",
        "Septembris",
        "Oktobris",
        "Novembris",
        "Decembris"
    ], ['prompt' => Yii::t('app', 'Choose month'), 'id' => 'invoices-month-selector']) ?>
    <button class="btn btn-primary pull-right" id="export-sent-invoices">Eksportēt uz CSV (eksportētas tiks visas <strong>redzamās</strong> rindas)</button>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'sent_date',
                        'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'sent_date',
                        'language' => 'lv',
                        'dateFormat' => 'yyyy-MM-dd',
                    ]),
                ],
                [
                    'attribute' => 'invoice_number',
                    'value' => function($dataProvider){    
                        $number = $dataProvider['invoice_number'];
                        return "<a target='_blank' href='/sys/sent-invoices/update?number=$number'>$number</a>";
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'user name',
                    'value' => function($dataProvider){
                        if(!$dataProvider['student']) return;
                        
                        return $dataProvider['student']['first_name'] . ' ' . $dataProvider['student']['last_name'];
                    },
                    'label' => Yii::t('app', 'Student')
                ],
                [
                    'attribute' => 'is_advance',
                    'value' => function($dataProvider){
                        return $dataProvider['is_advance'] ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
                    },
                    'filter' => Html::dropDownList('SentInvoicesSearch[is_advance]', isset($get['SentInvoicesSearch[is_advance]']) ? $get['SentInvoicesSearch[is_advance]'] : '', [0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                ],
                'plan_name',
                'plan_price',
            ],
            'options' => [
                'id' => 'sent-invoices-table',
            ],
        ]);
        ?>
</div>



