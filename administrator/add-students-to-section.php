<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();

$location = basename($_SERVER['REQUEST_URI']);

$semester = (isset($semester) ? $semester : '');
$school_year = (isset($school_year) ? $school_year : '');

if (($user->isLoggedIn() && !$user->hasPermission('Administrator')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

if (Input::exists() && Input::get('save_sy')) 
{
    if (!$student->checkSchoolYear(Input::get('school_year'), Input::get('semester'))) 
    {
        try 
        {
            (isset($sy_id) ? $student->updateSchoolYear(array('isCurrent' => 0), $sy_id) : '');

            $student->editSchoolYear(array(
                'schoolyear' => Input::get('school_year'),
                'semester' => Input::get('semester'),
                'isCurrent' => 1
            ));

            Session::flash('edit_sy_result', '<div class="alert alert-success">School Year and Semester has been updated successfully!</div>');
            Redirect::to($location);

        } 
        catch (Exception $e) 
        {
            Session::flash('edit_sy_result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    }
}

if (Input::exists() && Input::get('use_existing')) 
{
    try 
    {
        $sem = Input::get('existingSem');
        $sy = Input::get('existingSY');

        $checkAndGet = DB::getInstance()->query("SELECT * FROM school_year WHERE semester = ? AND schoolyear = ?", array($sem, $sy));

        if ($checkAndGet->count())
        {
            $id = $checkAndGet->first()->sy_id;

            $student->updateSchoolYear(array('isCurrent' => 0), $sy_id);
            $student->updateSchoolYear(array('isCurrent' => 1), $id);

            Session::flash('edit_sy_result', '<div class="alert alert-success">School Year settings has been changed successfully!</div>');
            Redirect::to($location);
        }
    } 
    catch (Exception $e) 
    {
        Session::flash('edit_sy_result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
        Redirect::to($location);
    }
}


if (Input::exists()) 
{
    if (Input::get('confirm')) 
    {
        try {
            $section_id = Input::get("section");
            $ids = Input::get('student_id');

            $sel_section = Input::get('selected-section');

            $count = 0;

            foreach($ids as $id) 
            {
                $student->addToSection(array(
                    'section_id' => $section_id,
                    'student_id' => $id,
                    'semester' => $semester,
                    'school_year' => $school_year
                ));

                $count++;
            }

            Session::flash('result', '<div class="alert alert-success">' . $count . ' student(s) has been added to <strong>' . $sel_section . '</strong></div>');
            Redirect::to($location);

        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    }
}

?>
<?php include 'header.php'; ?>
<body>
    <div id="progress" class="waiting">
        <dt></dt>
        <dd></dd>
    </div>

    <div id="wrapper">
        <?php include 'sidebar.php'; ?>
        <div id="content">
            <?php include 'navbar.php'; ?>
            <div class="container-fluid" id="_content" style="display: none;">
                <div class="swipe-area"></div>

                <?php 
                // Display result of SchoolYear/Semester Settings
                echo (Session::exists('edit_sy_result') ? Session::flash('edit_sy_result') : '');

                // Display if school year is not set
                echo (isset($sy_message) ? $sy_message : '');
                ?>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Students</li>
                        <li class="breadcrumb-item"><a href="sections.php"><i class="fas fa-table"></i> &nbsp;Sections</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-user-plus"></i> &nbsp;Add Students to Section</li>
                    </ol>
                </nav>
                <h4>Add Students to Section</h4>
                <hr>

                <?php 
                // Display result of appending students to selected section
                echo (Session::exists('result') ? Session::flash('result') : '');
                ?>

                <form action="" method="post">
                    <div class="row">
                        <div class="col-xl-5 col-lg-12 col-md-12 mb-3">
                            <div class="alert alert-primary"><h4>Sections</h4></div>
                            <table id="a_sections_list">
                                <thead>
                                    <th width="100"></th>
                                    <th class="text-center">Section Name</th>
                                    <th class="text-center">Year</th>
                                </thead>
                                <tbody class="tdata">
                                    <?php
                                    if ($student->getSections()) 
                                    {
                                        $count = 0;
                                        foreach($student->results() as $row) 
                                        {
                                    ?>
                                    <tr class="bottom_border">
                                        <td class="c-green">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" name="section" id="<?php echo str_replace(" ", "_", $row->section_name) ?>" value="<?php echo $row->section_id ?>">
                                                <label class="custom-control-label" for="<?php echo str_replace(" ", "_", $row->section_name) ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center c-green"><label for="<?php echo str_replace(" ", "_", $row->section_name) ?>"><?php echo $row->section_name ?></label></td>
                                        <td class="text-center c-green"><label for="<?php echo str_replace(" ", "_", $row->section_name) ?>"><?php echo $row->year ?></label></td>
                                    </tr>
                                    <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xl-7 col-lg-12 mb-3">
                            <div class="alert alert-primary"><h4>Students</h4></div>
                            <table border="0" id="a_students_list">
                                <thead class="text-center">
                                    <th width="100"></th>
                                    <th>Student #</th>
                                    <th class="text-center">Name</th>
                                </thead>
                                <tbody class="tdata text-center">
                                    <?php
                                    if ($student->getStudentsNotExistsInSections($school_year, $semester)) 
                                    {
                                        $count = 0;
                                        foreach($student->results() as $row) 
                                        {
                                            $mi = ($row->middlename == '' ? '' : substr($row->middlename, 0, 1) . '.');
                                    ?>
                                    <tr class="bottom_border">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="<?php echo $row->student_no ?>" name="student_id[<?php echo $row->student_id ?>]" value="<?php echo $row->student_id ?>">
                                                    <span class="form-check-sign">
                                                        <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="c-green"><label id="sample" for="<?php echo $row->student_no ?>"><?php echo $row->student_no ?></label></td>
                                        <td class="c-green"><label for="<?php echo $row->student_no ?>"><?php echo '<strong>' . $row->lastname . '</strong>, ' . $row->firstname . ' ' . $mi ?></label></td>
                                    </tr>
                                    <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xl-3">
                            <button type="button" class="btn btn-primary col-sm-12 save" id="btn_save_stsc">Save</button>

                            <!-- Confirmation modal -->
                            <div class="modal hide fade" id="saveModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-green">
                                            <h6 class="text-light">Confirm Selected</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="text-light">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Selected students will be added to <strong><label id="section__name"></label></strong>, proceed?</label>
                                            <input type="hidden" name="selected-section" id="selected-section">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" tabindex="1" name="confirm" value="true" id="confirm">OK</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/. Confirmation modal -->

                            <!-- Error modal -->
                            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-crimson">
                                            <h6 class="text-light" id="title"></h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="text-light">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <span id="message"></span>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" tabindex="1" id="ok_btn">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br><br>
                </form>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php'; ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#a_sections_list').DataTable({ 
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "order": [1, 'asc'],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('#a_students_list').DataTable({
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "order": [2, 'asc'],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });


            $('.save').click(function() {
                var checkbox = document.getElementsByTagName('input');
                count = 0;

                for (var i = 0; i < checkbox.length; i++) 
                {
                    if (checkbox[i].type == 'checkbox' && checkbox[i].checked == true) 
                    {
                        count++;
                    }
                }

                if (isOneChecked() == true) 
                {
                    if (count > 0) 
                    {
                        $('#saveModal').modal('toggle');
                    } 
                    else 
                    {
                        document.getElementById('message').innerHTML = 'Please select at least one student!';
                        document.getElementById('title').innerHTML = 'Select Student!';
                        $('#errorModal').modal('toggle');
                    }
                } 
                else 
                {
                    document.getElementById('message').innerHTML = 'Please select a section!';
                    document.getElementById('title').innerHTML = 'Select Section!';
                    $('#errorModal').modal('toggle');
                }
            });

            function isOneChecked() {
                var chx = document.getElementsByTagName('input');
                for (var i = 0; i < chx.length; i++) 
                {
                    if (chx[i].type == 'radio' && chx[i].checked) 
                    {
                        return true;
                    }
                }
                return false;
            }

            $('#saveModal').on('shown.bs.modal', function(e) {
                var $radio = $('input[name=section]:checked');
                var id = $radio.attr('id');
                id = id.replace('_', ' ');
                
                document.getElementById('section__name').innerHTML = id;
                document.getElementById('selected-section').value = id;
            });
        });
    </script>
</body>
</html>