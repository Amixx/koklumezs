<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = 'Lekciju piešķiršana';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=$this->title?></h1>
<p>
    <?= Html::a('Manuāli izsaukt automātisko lekciju piešķiršanu visiem studentiem', 
    ['/cron','send' => 1], 
    [
        'class' => 'btn btn-success',
        'target' => '_blank',
        'data' => [
            'confirm' => 'Are you sure ?',
        ]
    ]) ?>
</p>
<div class="grid-view">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Lietotājs</th>
                <th>Pēdējā lekcija</th>
                <th>Spēles reizes</th>
                <th>Sarežģītība</th>
                <?php foreach($evaluationsTitles as $et){ ?>
                <th><?=$et?></th>
                <?php } ?>
                <th>Spējas</th>
                <th class="action-column">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $a = 1;
            foreach($users as $id => $user){
                
                ?>
            <tr>
                <td><?=$a?></td>
                <td><?=$user['email']?></td>
                <td><?=isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->title : '<code>Not set</code>' ?></td>
                <td align="center"><?=$lastlectures[$id]['open_times']?></td>
                <td align="center"><?=isset($lastlectures[$id]) ? $lastlectures[$id]->lecture->complexity : '<code>Not set</code>'?></td>
                <?php foreach($evaluationsTitles as $etid => $et){ ?>
                <td align="center">
                    <?php if(isset($evaluations[$id][$etid])){
                        echo isset($evaluationsValues[$etid]) ? $evaluationsValues[$etid][$evaluations[$id][$etid]] : $evaluations[$id][$etid];
                    }else{
                        echo '<code>Not set</code>';
                    }  ?>
                </td>
                <?php } ?>
                <td align="center"><?=isset($goals[$id][$goalsnow]) ? array_sum($goals[$id][$goalsnow]) : '<code>Not set</code>' ?></td>
                <td align="center">
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"> </span>', 
                    ['/assign/userlectures','id' => $id], 
                    [
                        'title' => 'Apskatīt',                        
                    ]) ?> 
                    <?= Html::a('<span class="glyphicon glyphicon-wrench"> </span>', 
                    ['/cron/userlectures','id' => $id], 
                    [
                        'title' => 'Automātiska piešķiršana',
                        'data' => [
                            'confirm' => 'Are you sure ?',
                        ]
                    ]) ?> 
                </td>
            </tr>
            <?   $a++;
            }
        ?>
        </tbody>

    </table>
</div>