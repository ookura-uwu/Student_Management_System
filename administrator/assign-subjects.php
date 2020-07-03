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
    try {
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
            $subject_ids = Input::get("subject_id");
            $student_ids = Input::get('student_id');
            $year = Input::get('year');

            $sel_section = Input::get('selected-section');

            $subj_count = 0;
            $student_count = 0;

            foreach ($student_ids as $student_id) 
            {
                $year = $year[$student_id];
                foreach ($subject_ids as $subject_id) 
                {
                    if ($student->checkStudentSubject($subject_id, $student_id, $semester, $school_year)) 
                    {
                        $subj_count++;
                        continue;
                    } 
                    else 
                    {
                        $student->addToStudent(array(
                            'subject_id' => $subject_id,
                            'student_id' => $student_id,
                            'year_id' => $year,
                            'semester' => $semester,
                            'school_year' => $school_year
                        ));
                    }

                    $subj_count++;
                }

                if (!$student->checkStudentYear($student_id, $year, $semester, $school_year)) 
                {
                    $student->addStudentYear(array(
                        'student_id' => $student_id,
                        'year_id' => $year,
                        'semester' => $semester,
                        'school_year' => $school_year
                    ));
                }

                $student_count++;
            }

            $subj_count = $subj_count / $student_count;

            Session::flash('result', '<div class="alert alert-success">' . $subj_count . ' subject(s) has been added to <strong>' . $student_count . ' student(s)</strong></div>');
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
                        <li class="breadcrumb-item"><a href="subjects.php"><i class="fas fa-table"></i> &nbsp;Subjects</a></li>
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
                        <div class="col-xl-6 col-lg-12 col-md-12 mb-3">
                            <div class="alert alert-primary"><h4>Students</h4></div>
                            <table id="s_students_list">
                                <thead class="text-center">
                                    <th width="100"></th>
                                    <th>Name</th>
                                    <th>Program</th>
                                </thead>
                                <tbody class="tdata">
                                    <?php
                                    if ($student->getList()) {
                                        foreach($student->results() as $row) {
                                            $mi = ($row->middlename == '' ? '' : substr($row->middlename, 0, 1) . '.');
                                            
                                            $program = '';
                                            if ($row->program == 'Bachelor of Science in Computer Science')
                                                $program = 'BSCS';
                                            else if ($row->program == 'Bachelor of Technical Teacher Education')
                                                $program = 'BTTE';
                                            else if ($row->program == 'Bachelor of Technical Vocational Teacher Education')
                                                $program = 'BTVTE';
                                    ?>
                                    <tr class="c-green">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <label class="form-check-label chx_st">
                                                    <input type="checkbox" class="form-check-input" id="<?php echo $row->student_no ?>" name="student_id[<?php echo $row->student_id ?>]" value="<?php echo $row->student_id ?>">
                                                    <span class="form-check-sign">
                                                        <span class="check"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td><label for="<?php echo $row->student_no ?>"><input type="hidden" name="year[<?php echo $row->student_id ?>]" value="<?php echo $row->year_id ?>"><?php echo '<strong>' . $row->lastname . '</strong>, ' . $row->firstname . ' ' . $mi ?></label></td>
                                        <td class="text-center"><label for="<?php echo $row->student_no ?>"><?php echo $program ?></label></td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xl-6 col-lg-12 mb-3">
                            <div class="alert alert-primary"><h4>Subjects</h4></div>
                            
                                <table id="s_subjects_list">
                                    <thead class="text-center">
                                        <th width="60"></th>
                                        <th width="100">Subject Code</th>
                                        <th>Section</th>
                                        <th>Sem/SY</th>
                                    </thead>
                                    <tbody class="text-center tdata">
                                        <?php
                                        if ($student->getSubjects($semester, $school_year)) 
                                        {
                                            foreach ($student->results() as $row) 
                                            {
                                                $program = '';
                                                $sem = '';
                                                $sy = '';

                                                if ($row->program == 'Bachelor of Science in Computer Science')
                                                    $program = 'BSCS';
                                                else if ($row->program == 'Bachelor of Technical Teacher Education')
                                                    $program = 'BTTE';
                                                else if ($row->program == 'Bachelor of Technical Vocational Teacher Education')
                                                    $program = 'BTVTE';

                                                if ($row->semester == '1st Semester')
                                                    $sem = '1st';
                                                else if ($row->semester == '2nd Semester')
                                                    $sem = '2nd';

                                                $pieces = explode("-", $row->school_year);
                                                $prev = $pieces[0];
                                                $next = str_replace("20", '', $pieces[1]);
                                                $sy = $prev . '-' . $next;
                                        ?>
                                        <tr class="c-green text-center">
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label chx_subj">
                                                        <input type="checkbox" class="form-check-input" id="<?php echo $row->subject_id ?>" name="subject_id[<?php echo $row->subject_id ?>]" value="<?php echo $row->subject_id ?>">
                                                        <span class="form-check-sign">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td><label for="<?php echo $row->subject_id ?>"><?php echo $row->subject_code ?></label></td>
                                            <td><label for="<?php echo $row->subject_id ?>"><?php echo $row->section_name ?></td>
                                            <td><label for="<?php echo $row->subject_id ?>"><?php echo $sem . " ({$sy})" ?></label></td>
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
                            <button type="button" class="btn btn-primary col-sm-12 save" id="btn_save_sts">Save</button>

                            <div class="modal fade" id="saveModal" tabindex="-1" role="document" aria-labelledby="saveModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-sm" id="confirmLocation" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-green">
                                            <h6 class="text-light">Confirm Selected</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="text-light">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Selected subjects will be assigned to students, proceed?</label>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" tabindex="1" name="confirm" value="true" id="confirm">OK</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error modal -->
                            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-sm" id="errorLocation" role="document">
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
                </form>
                <br><br>
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
            $('#s_subjects_list').DataTable({ 
                "lengthMenu": [[8, 10, 25, 50], [8, 10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('#s_students_list').DataTable({
                "lengthMenu": [[8, 10, 25, 50], [8, 10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('.save').click(function() {
            
                if (stChx() && subjChx()) {
                    $('#saveModal').modal('toggle');
                } else {
                    if (!stChx()) {
                        document.getElementById('title').innerHTML = 'Please select a student';
                        document.getElementById('message').innerHTML = 'Please select at least one student!';
                        $('#errorModal').modal('toggle');
                    } else if (!subjChx()) {
                        document.getElementById('title').innerHTML = 'Please select subject';
                        document.getElementById('message').innerHTML = 'Please select at least one subject!';
                        $('#errorModal').modal('toggle');
                    }
                }
            });

            function stChx() {
                var st_chx = document.querySelectorAll('.chx_st input');
                st_count = 0;

                for (var i = 0; i < st_chx.length; i++) {
                    if (st_chx[i].type == 'checkbox' && st_chx[i].checked == true) {
                        st_count++;
                    }
                }
                return (st_count > 0) ? true : false;
            }

            function subjChx() {
                var subj_chx = document.querySelectorAll('.chx_subj input');
                subj_count = 0;

                for (var i = 0; i < subj_chx.length; i++) {
                    if (subj_chx[i].type == 'checkbox' && subj_chx[i].checked == true) {
                        subj_count++;
                    }
                }
                return (subj_count > 0) ? true : false;
            }
        });
    </script>
</body>
</html>