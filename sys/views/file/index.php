<?php

use yii\helpers\Url;

$this->title = 'Notis/Sheet music';
$this->params['breadcrumbs'][] = ['label' => 'Notis/Sheet music', 'url' => ['index']];

?>

<div class="row">
    <?php if ($lecturefiles) { ?>
        <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?>
    <?php } ?>
</div>
</div>