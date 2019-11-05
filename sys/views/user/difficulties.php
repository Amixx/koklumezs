<h2>Parametri šobrīd</h2>      
    <?php      
    foreach($difficulties as $id => $name){  ?>
    <div class="form-group field-studentgoals">
        <label class="control-label"for="studentgoals-title-now<?=$id?>"><?=$name?></label>
        <select id="studentgoals-title-now<?=$id?>" class="form-control" name="studentgoals[now][<?=$id?>]" aria-required="true" aria-invalid="false">
            <option value=""></option>
            <?php for($a=1;$a<=10;$a++){ ?>
            <option value="<?=$a?>" <?=(isset($studentGoals['Šobrīd'][$id]) AND  ($studentGoals['Šobrīd'][$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
            <?php } ?>
        </select>
        <div class="help-block"></div>
    </div>                       
<?php } ?>       

    <h2>Parametri vēlamie</h2>      
    <?php      
    foreach($difficulties as $id => $name){ ?>
    <div class="form-group field-studentgoals">
        <label class="control-label"for="studentgoals-title-future<?=$id?>"><?=$name?></label>
        <select id="studentgoals-title-future<?=$id?>" class="form-control" name="studentgoals[future][<?=$id?>]" aria-required="true" aria-invalid="false">
            <option value=""></option>
            <?php for($a=1;$a<=10;$a++){ ?>
            <option value="<?=$a?>" <?=(isset($studentGoals['Vēlamais'][$id]) AND  ($studentGoals['Vēlamais'][$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
            <?php } ?>
        </select>
        <div class="help-block"></div>
    </div>
<?php } ?>