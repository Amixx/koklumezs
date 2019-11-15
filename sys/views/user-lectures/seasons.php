<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#seasonCollapse" aria-expanded="<?= empty($seasonSelected) ? 'false' : 'true' ?>" aria-controls="seasonCollapse">
    Gadskārta
</button>
<div class="<?= empty($seasonSelected) ? 'collapse' : '' ?>" id="seasonCollapse">
    <div class="card card-body">
        <h2>Gadskārta</h2>
        <hr />      
        <div class="form-group field-season">
            <label class="control-label"for="season-title">Gadskārta</label>
            <select id="season-title" class="form-control" name="season" aria-required="true" aria-invalid="false">
                <option value=""></option>
                <?php foreach($seasons as $season){ ?>
                <option value="<?= $season?>" <?=(isset($seasonSelected) AND ($seasonSelected == $season)) ? 'selected' : ''?>><?=$season?></option>
                <?php } ?>
            </select>
            <div class="help-block"></div>
        </div> 
    </div>
</div>
