<?php
 require 'school-year.php'; ?>
<nav class="navbar navbar-expand-lg" style="background-color: #21561e">
    <div class="container-fluid">

        <button type="button" id="sidebarCollapse" class="btn " style="background-color: #21561e">
            <i class="fas fa-align-justify"></i>
            <span>Toggle Sidebar</span>
        </button>
        <span class="text-light text-center">S.Y <?php echo (isset($school_year) ? $school_year : '') . ' - ' . (isset($semester) ? $semester : ''); ?></span>&nbsp;
        <button class="btn btn-sm text-light mb-2" style="background-color: #21561e" data-toggle="modal" data-target="#editSYModal" id="edit" title="Update School Year" data-placement="bottom"><i class="fas fa-edit"></i></button>
        <div class="row">
            <form action="" method="post">
                <div class="col-auto">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <select class="form-control form-control-sm btn-success bg-success text-light mb-2" 
                                    name="existingSem" 
                                    id="existingSem"
                                    onchange="checkSY(document.getElementById('existingSY').value, document.getElementById('existingSem').value)">
                                <option value="">Select Semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>&nbsp;
                            <select class="form-control form-control-sm btn-success bg-success text-light mb-2" 
                                    name="existingSY" 
                                    id="existingSY" 
                                    onchange="checkSY(document.getElementById('existingSY').value, document.getElementById('existingSem').value)">
                                <option value="">Select School Year</option>
                                <?php
                                $get = DB::getInstance()->query("SELECT DISTINCT schoolyear FROM school_year ORDER BY schoolyear DESC");
                                if ($get->count()) {
                                    foreach($get->results() as $row) {
                                ?>
                                <option value="<?php echo $row->schoolyear ?>"><?php echo $row->schoolyear ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>&nbsp;
                        <button type="submit" class="btn btn-success btn-sm mb-2" id="btn_existing_sy" name="use_existing" value="true">Go</button>
                        <label class="ml-2 mt-1" id="result"></label>
                    </div>
                </div>
            </form>
        </div>
        <button class="btn btn-sm d-inline-block d-lg-none ml-auto text-light" style="background-color: #21561e" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-align-justify"></i>
        </button>

        <div class="modal fade" id="editSYModal" tabindex="-1" role="document" aria-labelledby="editSYModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-sm" id="editSYModalLocation" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6>Edit School Year</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" data-toggle="validator" id="sy">
                            <div class="form-row align-items-center">
                                <div class="col-xl-4 col-lg-4 col-md-4 mb-2">
                                    <label class="sr-only" for="inlineSY">School Year:</label>
                                    <input type="text" class="form-control" name="school_year" id="inlineSY" placeholder="School Year" required>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 mb-2">
                                    <select class="form-control custom-select" name="semester">
                                        <option value="">Select Semester</option>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 mb-2">
                                    <input type="hidden" name="token_sy" value="<?php echo Token::generate() ?>">
                                    <button type="submit" class="btn btn-primary col-sm-12" name="save_sy" value="true" id="save">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse mb-2" id="navbarSupportedContent">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-light text-right" href="account_settings.php">Account Settings <i class="fas fa-cogs"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light text-right" href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>