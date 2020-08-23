<?php

use yii\helpers\Url;

$this->title = \Yii::t('app',  'Sheet music');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Sheet music'), 'url' => ['index']];

?>

<div class="row">
    <?php if ($lecturefiles) { ?>
        <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
    <?php } ?>
</div>
</div>