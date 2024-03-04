<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <h4><?= $breadcrumbs; ?></h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for=""><?= $this->lang->line('ms_year'); ?></label>
                            <select name="" id="" class="form-control" data-plugin="select_hrm" data-placeholder="<?= $this->lang->line('ms_title_select_year'); ?>">
                                <?php
                                $usedYear = ['2022', '2024'];

                                for ($i = 2020; $i <= date('Y'); $i++) {
                                    $selected = '';
                                    if ($i == date('Y') and !in_array($i, $usedYear)) {
                                        $selected = 'selected';
                                    }
                                    if (!in_array($i, $usedYear)) {
                                        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>