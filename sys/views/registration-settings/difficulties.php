<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

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
                    'view' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => 'View', 'data-pjax' => '0']
                        );
                    },
                    'update' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"> </span>',
                            $url,
                            ['title' => 'Update', 'data-pjax' => '0']
                        );
                    },
                    'delete' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"> </span>',
                            $url,
                            ['title' => 'Delete', 'data-pjax' => '0']
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    if ($action === 'view') {
                        return Url::base(true) . '/difficulties/view/' . $model['id'];
                    }

                    if ($action === 'update') {
                        return Url::base(true) . '/difficulties/update/' . $model['id'];
                    }
                    if ($action === 'delete') {
                        return Url::base(true) . '/difficulties/delete/' . $model['id'];
                    }
                }
            ],
        ],
    ]); ?>


</div>