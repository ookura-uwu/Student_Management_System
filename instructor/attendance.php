<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

if (($user->isLoggedIn() && !$user->hasPermission('Instructor')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

$instructor->getInstructorByUserId($user->data()->user_id);
$data = $instructor->data();

$location = basename($_SERVER['REQUEST_URI']);

if (Input::exists()) 
{
    if (Input::get('present')) 
    {
        $students_id = Input::get('student_id');
        $year_id = Input::get('year_id');

        try 
        {
            foreach($students_id as $id) 
            {
                if ($instructor->checkAttendance($id, Input::get('subject'), $data->instructor_id, $time->format('m/d/Y'), $semester, $school_year)) 
                {
                    // Update student's attendance
                    $instructor->updateAttendance($id, Input::get('subject'), $data->instructor_id, 'Present', $time->format('m/d/Y'), $semester, $school_year);
                } 
                else 
                {
                    $instructor->addAttendance(array(
                        'student_id' => $id,
                        'subject_id' => Input::get('subject'),
                        'instructor_id' => $data->instructor_id,
                        'description' => "Present",
                        'day' => $time->format('m/d/Y'),
                        'year_id' => $year_id[$id],
                        'semester' => $semester,
                        'school_year' => $school_year
                    ));
                }
            }

            Session::flash('result', '<div class="alert alert-success">Selected student(s) has been successfully marked as <strong>Present</strong>!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }

    } 
    else if (Input::get('absent')) 
    {
        $students_id = Input::get('student_id');
        $year_id = Input::get('year_id');

        try 
        {
            foreach($students_id as $id) 
            {
                if ($instructor->checkAttendance($id, Input::get('subject'), $data->instructor_id, $time->format('m/d/Y'), $semester, $school_year)) 
                {
                    // Update student's attendance
                    $instructor->updateAttendance($id, Input::get('subject'), $data->instructor_id, 'Absent', $time->format('m/d/Y'), $semester, $school_year);
                } 
                else 
                {
                    $instructor->addAttendance(array(
                        'student_id' => $id,
                        'subject_id' => Input::get('subject'),
                        'instructor_id' => $data->instructor_id,
                        'description' => "Absent",
                        'day' => $time->format('m/d/Y'),
                        'year_id' => $year_id[$id],
                        'semester' => $semester,
                        'school_year' => $school_year
                    ));

                }
            }

            Session::flash('result', '<div class="alert alert-success">Selected student(s) has been successfully marked as <strong>Absent</strong>!</div>');
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
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><a href="my-subjects.php"><i class="fas fa-table"></i> &nbsp;My Subjects</a></li>
                        <li class="breadcrumb-item"><a href="view-students.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') : '')  ?>"><i class="fas fa-users"></i> &nbsp;View Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-tasks"></i>Attendance</li>
                    </ol>
                </nav>
                <h4>Attendance</h4>
                <hr>
                
                <div class="form-row">
                    <form action="" method="get">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Subject:</span>
                                    </div>
                                    <select class="custom-select" id="subject" width="100%" name="subject" onchange="this.form.submit()">
                                        <option value="default">Select Subject</option>
                                        <?php
                                        if ($instructor->getInstructorSubjects($data->instructor_id, $semester, $school_year)) 
                                        {
                                            foreach ($instructor->results() as $row) 
                                            {
                                        ?>
                                        <option value="<?php echo $row->subject_id ?>" <?php (Input::exists('get') ? (Input::get('subject') == $row->subject_id ? print 'selected' : '') : '') ?>><?php echo $row->subject_code ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <br>

                <?php echo (Session::exists('result') ? Session::flash('result') : '') ?>
                <form action="" method="post">
                    <ul class="nav justify-content-center bg-dark"><li><h4 class="text-light"><?php echo $time->format('m/d/Y') ?></h4></li></ul>
                    <ul class="nav justify-content-center bg-dark sticky-top" id="mark-as_absent-present">
                        <li class="nav-item"><a role="button" id="mark_as_present" class="nav-link btn-link text-primary"><i class="fas fa-check"></i>&nbsp;Mark as Present</a></li>
                        <li class="nav-item"><a role="button" id="mark_as_absent" class="nav-link btn-link text-warning"><i class="fas fa-times"></i>&nbsp;Mark as Absent</a></li>
                    </ul>
                    
                    <hr>
                    <!-- <div class="table-responsive"> -->
                        <table class="" border="0" id="att_students_list">
                            <thead class="text-center">
                                <th></th>
                                <th>Full Name</th>
                                <th>Description</th>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                if (Input::exists('get') && Input::get('subject') != 'default') 
                                {
                                    if ($instructor->getStudentsBySubject(Input::get('subject'), $school_year, $semester)) 
                                    {
                                        $count = 0;
                                        foreach($instructor->results() as $row) 
                                        {
                                            if ($instructor->checkAttendance($row->student_id, Input::get('subject'), $data->instructor_id, $time->format('m/d/Y'), $semester, $school_year))
                                                $desc = $instructor->first()->description;
                                            else
                                                $desc = 'Undefined';

                                            if ($count % 2 == 0) 
                                            {
                                                echo "<tr class='bottom_border row-gray'>";
                                            } 
                                            else 
                                            {
                                                echo "<tr class='bottom_border'>";
                                            }

                                            $fullname = strtoupper($row->lastname) . ', ' . $row->firstname . ' ' . $row->middlename;

                                            echo "<td class='text-center'>";
                                            echo "<div class='form-check'>";
                                            echo "<label class='form-check-label'>";
                                            echo "<input type='checkbox' class='form-check-input' id='{$row->student_no}' name='student_id[{$row->student_id}]' value='{$row->student_id}'>";
                                            echo '<span class="form-check-sign">';
                                            echo '<span class="check"></span>';
                                            echo '</span>';
                                            echo '</label>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo "<td><label for='{$row->student_no}'>
                                                        <input type='hidden' id='{$row->student_no}' name='year_id[{$row->student_id}]' value='{$row->year_id}'>{$fullname}</label></td>";
                                            echo "<td><label for='{$row->student_no}'>{$desc}</label></td>";
                                            echo '</tr>';

                                            $count++;
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    <!-- </div> -->

                    <!-- Present Modal -->
                    <div class="modal fade" id="presentModal" tabindex="-1" role="document" aria-hidden="true">
                        <div class="modal-dialog" role="dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-header-green">
                                    <h4 class="text-light">Mark as Present</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span class="text-light">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Selected students will be marked as <strong>Present</strong>, proceed?
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="present" value="Present">OK</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.Present Modal -->

                    <!-- Absent Modal -->
                    <div class="modal fade" id="absentModal" tabindex="-1" role="document" aria-hidden="true">
                        <div class="modal-dialog" role="dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-header-green">
                                    <h4 class="text-light">Mark as Absent</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span class="text-light">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Selected students will be marked as <strong>Absent</strong>, proceed?
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="absent" value="Absent">OK</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.Absent Modal -->

                    <!-- Error Modal -->
                    <div class="modal fade" id="errorModal" tabindex="-1" role="document" aria-hidden="true">
                        <div class="modal-dialog" role="dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-header-crimson">
                                    <h4 class="text-light">Select Students</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span class="text-light">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Please select at least one student!
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
            $('#att_students_list').DataTable({
                'order': [[1, 'asc']],
                "lengthMenu": [[10, 25, 50], [10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bAutoWidth": false
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('#mark_as_present').click(function() {
                var chk = document.getElementsByTagName('input');
                count = 0;

                for (var i = 0; i < chk.length; i++) 
                {
                    if (chk[i].type == 'checkbox' && chk[i].checked) 
                    {
                        count++;
                    }
                }

                if (count > 0) 
                {
                    $('#presentModal').modal('toggle');
                    document.getElementById('test').value = 'Present';
                } 
                else 
                {
                    $('#errorModal').modal('toggle');
                }
            });

            $('#mark_as_absent').click(function() {
                var chk = document.getElementsByTagName('input');
                count = 0;

                for (var i = 0; i < chk.length; i++) 
                {
                    if (chk[i].type == 'checkbox' && chk[i].checked) 
                    {
                        count++;
                    }
                }

                if (count > 0) 
                {
                    $('#absentModal').modal('toggle');
                    document.getElementById('absent').value = 'Absent';
                }
                else
                    $('#errorModal').modal('toggle');
            });

        });
    </script>
</body>
</html>