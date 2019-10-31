<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\User */

$this->title = 'Lietotāji';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a('Izveidot lietotāju', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
        $status = [10 => 'Aktīvs', 9 => 'Nav aktīvs', 0 => 'Dzēsts'];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'first_name',
            'last_name',
            'phone_number',
            'email:email',            
            [
                'attribute' => 'user_level',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->user_level == 'Student' ? 'Students' : 'Administrators';
                },
                'filter'=> Html::dropDownList('UserSearch[user_level]',isset($get['UserSearch']['user_level']) ? $get['UserSearch']['user_level'] : '' ,app\models\User::getLevels(),['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ],     
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->status == '10' ? '<span style="color:green;">Aktīvs</span>' : '<span style="color:red;">Nav aktīvs</span>';
                },
                'filter'=> Html::dropDownList('UserSearch[status]',isset($get['UserSearch']['status']) ? $get['UserSearch']['status'] : '' ,app\models\User::getStatus(),['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ],           
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
