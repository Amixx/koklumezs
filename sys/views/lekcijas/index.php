<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lekcijas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Nosaukums</th>
                <th></th>
            </tr>        
        </thead>
        <tbody>
            <?php foreach($models as $model){ ?>
            <tr>
                <td><?=$model->title?></td>
                <td></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
    // display pagination
    echo LinkPager::widget([
        'pagination' => $pages,
    ]); ?>
</div>
