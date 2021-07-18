<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;

$this->title = \Yii::t('app',  'School settings');

?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create emails'), ['/school-registration-emails/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="tab-pane fade" id="bank-account" role="tabpanel" aria-labelledby="bank-account-tab">
        <h1><?= Yii::t("app", "School requisites") ?></h1>
        <table class="table table-striped table-bordered">
            <?php foreach ($emails as $key => $value) { ?>
                <tr>
                    <th scope="row"><?= $key ?></th>
                    <td><?= $value ?></td>
                </tr>
            <?php } ?>
        </table>
        <?= Html::a(\Yii::t('app',  'Edit'), ['bank-update'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>