<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DifficultiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'School settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered">
        <tr>
            <?php foreach ($settings as $key => $setting) { ?>
                <th>
                    <?= $key ?>
                </th>
            <?php } ?>
        </tr>
        <tr>
            <?php foreach ($settings as $setting) { ?>
                <td>
                    <?= $setting ?>
                </td>
            <?php } ?>
        </tr>
    </table>

    <?= Html::a(\Yii::t('app',  'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>

    <hr>
    <p>
        <strong><?= Yii::t('app', 'The link that students can use to join this school'); ?>: </strong>
        <code>https://skola.koklumezs.lv/sys/site/sign-up?s=<?= $schoolId ?>?l=<?= Yii::$app->language ?></code>
    </p>
    <hr>    

    <?= $this->render("difficulties", [
        'dataProvider' => $difficultiesDataProvider
    ]) ?>
</div>