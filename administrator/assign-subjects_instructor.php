<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

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
        try 
        {
            $instructor_id = Input::get("instructor");
            $subject_ids = Input::get('subject_id');

            $sel_instructor = Input::get('selected-instructor');

            $count = 0;
            foreach ($subject_ids as $subject_id) 
            {
                if ($instructor->checkInstructorSubject($subject_id, $instructor_id, $semester, $school_year)) 
                {
                    continue;
                } 
                else 
                {
                    $instructor->addSubject(array(
                        'instructor_id' => $instructor_id,
                        'subject_id' => $subject_id,
                        'semester' => $semester,
                        'school_year' => $school_year
                    ));
                }

                $count++;
            }

            Session::flash('result', '<div class="alert alert-success">' . $count . ' subject(s) has been added to <strong>' . $sel_instructor . '</strong></div>');
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
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Instructors</li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-file-alt"></i> &nbsp;Assign Subjects</li>
                    </ol>
                </nav>
                <h4>Assign Subjects</h4>
                <hr>

                <?php 
                // Display result of appending students to selected section
                echo (Session::exists('result') ? Session::flash('result') : '');
                ?>

                <form action="" method="post">
                    <div class="row">
                        <div class="col-xl-4 col-lg-12 col-md-12 mb-3">
                            <div class="alert alert-primary"><h4>Instructors</h4></div>
                            <table id="a_instructors_list">
                                <thead>
                                    <th class="text-center" width="100"></th>
                                    <th class="text-center">Instructor Name</th>
                                </thead>
                                <tbody class="tdata">
                                    <?php
                                    if ($instructor->getInstructors()) 
                                    {
                                        foreach ($instructor->results() as $row) 
                                        {
                                    ?>
                                    <tr class="c-green">
                                        <td class="text-center">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" name="instructor" id="<?php echo $row->lastname . ',_' . $row->firstname ?>" value="<?php echo $row->instructor_id ?>">
                                                <label class="custom-control-label" for="<?php echo $row->lastname . ',_' . $row->firstname ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <label for="<?php echo $row->lastname . ',_' . $row->firstname ?>">
                                                <?php echo $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename ?>        
                                            </label>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xl-8 col-lg-12 mb-3">
                            <div class="alert alert-primary"><h4>Subjects</h4></div>
                            <table id="ai_subjects_list">
                                <thead class="text-center">
                                    <th width="100"></th>
                                    <th width="110">Subject Code</th>
                                    <th width="200">Schedule</th>
                                    <th width="120">Section</th>
                                </thead>
                                <tbody class="text-center tdata">
                                    <?php
                                    $get = DB::getInstance()->query("SELECT * FROM view_subjects WHERE semester = ? AND school_year = ? ORDER BY subject_code ASC", array($semester, $school_year));

                                    if ($get->count()) {
                                        foreach($get->results() as $row) {
                                            if ($row->class_days == 'Mon/Tue/Wed/Thu/Fri') {
                                                $days = 'Mon - Fri';
                                            } else if ($row->class_days == 'Mon/Tue/Wed/Thu/Fri/Sat') {
                                                $days = 'Mon - Sat';
                                            } else {
                                                $days = $row->class_days;
                                            }

                                            $section = str_replace(' ', '', $row->section_name);
                                            $code = str_replace(' ', '', $row->subject_code) . $section;
                                    ?>
                                    <tr class="c-green">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="<?php echo $code ?>" name="subject_id[<?php echo $row->subject_id ?>]" value="<?php echo $row->subject_id ?>">
                                                    <span class="form-check-sign">
                                                        <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td><label for="<?php echo $code ?>"><?php echo $row->subject_code ?></label></td>
                                        <td><label for="<?php echo $code ?>"><?php echo $days . ' | ' . $row->class_starts . ' - ' . $row->class_ends ?></td>
                                        <td><label for="<?php echo $code ?>"><?php echo $row->section_name ?></label></td>
                                    </tr>
                                    <?php
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
                            <button type="button" class="btn btn-primary col-sm-12 save" id="btn_save_inss">Save</button>

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
                                            <label>Selected subjects will be assigned to <strong><label id="instructor__name"></label></strong>, proceed?</label>
                                            <input type="hidden" name="selected-instructor" id="selected-instructor">
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
                                            <h6 id="title" class="text-light"></h6>
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
            $('#a_instructors_list').DataTable({ 
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false 
            });

            $('#ai_subjects_list').DataTable({
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": true,
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
                        document.getElementById('title').innerHTML = 'Select Subject';
                        document.getElementById('message').innerHTML = 'Please select at least one subject!';
                        $('#errorModal').modal('toggle');
                    }
                } 
                else 
                {
                    document.getElementById('title').innerHTML = 'Select Instructor';
                    document.getElementById('message').innerHTML = 'Please select an instructor!';
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
                var $radio = $('input[name=instructor]:checked');
                var id = $radio.attr('id');
                id = id.replace('_', ' ');
                
                document.getElementById('instructor__name').innerHTML = id;
                document.getElementById('selected-instructor').value = id;
            });
        });
    </script>
</body>
</html>