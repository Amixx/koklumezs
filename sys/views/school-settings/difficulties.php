<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DifficultiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Parameters');

?>
<div class="difficulties-index">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a(\Yii::t('app', 'Create parameter'), ['/difficulties/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => 'View', 'data-pjax' => '0']
                        );
                    },
                    'update' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"> </span>',
                            $url,
                            ['title' => 'Update', 'data-pjax' => '0']
                        );
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"> </span>',
                            $url,
                            ['title' => 'Delete', 'data-pjax' => '0']
                        );
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = '/sys/difficulties/view/' . $model['id'];
                        return $url;
                    }

                    if ($action === 'update') {
                        $url = '/sys/difficulties/update/' . $model['id'];
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = '/sys/difficulties/delete/' . $model['id'];
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>


</div>