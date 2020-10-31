<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = $subplan['plan']['name'];
\yii\web\YiiAsset::register($this);
?>
<div class="difficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $subplan,
        'attributes' => [
            'start_date',
            'sent_invoices_count',
            'times_paid',
            'plan.name',
            [
                'attribute' => 'plan.files',
                'value' => function($subplan){
                    if(!$subplan['plan']['files']) return "";
                    $files = explode(", ", $subplan['plan']['files']);
                    $html = "";
                    foreach($files as $file){
                        $html .= "<a href=". $file .">". $file ."</a> ";
                    }
                    return $html;
                },
                'format' => 'html'
            ]
        ],
    ]) ?>
</div>