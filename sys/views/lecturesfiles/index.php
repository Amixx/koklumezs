<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesfilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'Files');

?>
<div class="lecturesfiles-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Add file'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title:ntext',
            'file:ntext',
            [
                'attribute' => 'thumb',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::img(
                        $data['thumb'],
                        ['width' => '70px']
                    );
                },
            ],
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList(
                    'LecturesfilesSearch[lecture_id]',
                    isset($get['LecturesfilesSearch']['lecture_id'])
                        ? $get['LecturesfilesSearch']['lecture_id']
                        : '',
                    $lectures,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>