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


if (Input::exists('get')) {
    if (Input::get('type') == 'student') {
        if (is_numeric(Input::get('subject'))) {
            try {
                $student->removeSubjectFromStudent(Input::get('subject'));

                Session::flash('result', '<div class="alert alert-success">Selected subject has been removed from student!</div>');
                Redirect::to('view-subjects-list.php?student=' . Input::get('student') . '&year=' . Input::get('year') . '&type=student');
            } catch (Exception $e) {
                
            }
        }
    } else if (Input::get('type') == 'instructor') {
        if (is_numeric(Input::get('subject'))) {
            try {
                $instructor->removeSubjectFromInstructor(Input::get('subject'));

                Session::flash('result', '<div class="alert alert-success">Selected subject has been removed from instructor!</div>');
                Redirect::to('view-subjects-list.php?instructor=' . Input::get('instructor'));
            } catch (Exception $e) {
                Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
                Redirect::to('view-subjects-list.php?instructor=' . Input::get('instructor'));
            }
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
                    <?php
                    if (Input::exists('get')) 
                    {
                        if (Input::get('type') == 'student') 
                        {
                    ?>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><a href="students.php"><i class="fas fa-users"></i> &nbsp;Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Student's Subjects</li>
                    </ol>
                    <?php
                        } 
                        else if (Input::get('type') == 'instructor') 
                        {
                    ?>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><a href="instructors-list.php"><i class="fas fa-users"></i> &nbsp;Instructors</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Instructor's Subjects</li>
                    </ol>
                    <?php
                        }
                    }
                    ?>
                </nav>
                <?php
                echo (Session::exists('result') ? Session::flash('result') : '');
                ?>
                <h4>Current Subjects</h4>
                <hr>
                <div class="container">
                    <table id="v_subject_current_list">
                        <thead class="text-center">
                            <th>Subject Code</th>
                            <!-- <th>Subject Name</th>
                            <th>Units</th>
                            <th>Schedule</th>
                            <th>Section</th> -->
                            <th>Subject Name</th>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            if (Input::exists('get')) 
                            {
                                $name = '';
                                if (Input::get('type') == 'student') 
                                {
                                    if ($student->getStudentCurrentSubjects(Input::get('student'), Input::get('year'), $semester, $school_year)) {
                                        $count = 0;
                                        foreach ($student->results() as $row) 
                                        {
                                            $code = str_replace(' ', '', $row->subject_code);
                                            if ($count % 2 == 0) 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle row-gray bottom_border'>";
                                            } 
                                            else 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                            }
                            ?>
                                    <td><?php echo $row->subject_code ?></td>
                                    <td><?php echo $row->subject_name ?></td>
                                </tr>
                                <tr class="child-color">
                                    <td>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Units:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Section:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Schedule:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Actions:
                                        </div>
                                    </td>

                                    <td>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->units ?>
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->section_name ?>
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->class_days . ' | ' . $row->sched ?>
                                        </div>
                                        <div class="accordian-body collapse <?php echo $code ?> text-center">
                                            <a role="button" class="btn btn-link text-danger" data-href="?student=<?php echo Input::get('student'); ?>&year=<?php echo Input::get('year') ?>&subject=<?php echo $row->student_subject_id ?>&type=student" data-toggle="modal" data-target="#removeModal">
                                                Remove Subject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                            $count++;
                                        }
                                    }
                                } 
                                else if (Input::get('type') == 'instructor') 
                                {
                                    if ($instructor->getInstructorSubjects(Input::get('instructor'), $semester, $school_year)) 
                                    {
                                        $count = 0;
                                        foreach ($instructor->results() as $row) 
                                        {
                                            $code = str_replace(' ', '', $row->subject_code);

                                            if ($count % 2 == 0) 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle row-gray bottom_border'>";
                                            }
                                            else 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                            }
                            ?>
                                    <td><?php echo $row->subject_code ?></td>
                                    <td><?php echo $row->subject_name ?></td>
                                </tr>
                                <tr class="child-color">
                                    <td>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Units:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Section:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Schedule:
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            Actions:
                                        </div>
                                    </td>
                                    <td>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->units ?>
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->section_name ?>
                                        </div>
                                        <div class='accordian-body collapse <?php echo $code ?> text-center'>
                                            <?php echo $row->sched ?>
                                        </div>
                                        <div class="accordian-body collapse <?php echo $code ?> text-center">
                                            <a class="btn btn-link btn-sm text-danger" data-href="?instructor=<?php echo Input::get('instructor'); ?>&subject=<?php echo $row->instructor_subject_id ?>&type=instructor" data-toggle="modal" data-target="#removeModal">
                                                Remove Subject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                            $count++;
                                        } 
                                    }
                                }
                            }
                            ?>
                        </tbody>
                        <div class="modal fade" id="removeModal" tabindex="-1" role="document" aria-labelledby="removeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-crimson">
                                        <h6 class="text-light">Remove Subject</h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="text-light">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Selected subject will be removed from 
                                            <strong>
                                                <?php
                                                if (isset($row->student_name)) 
                                                {
                                                    echo $row->student_name;
                                                } 
                                                else if (isset($row->instructor_name)) 
                                                {
                                                    echo $row->instructor_name;
                                                } 
                                                else 
                                                {
                                                    echo '';
                                                }
                                                ?>
                                            </strong>, proceed?</label>
                                        <input type="hidden" name="instructor_name" value="<?php echo $row->instructor_name ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <a role="submit" class="btn btn-danger remove">OK</a>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </table>
                </div>
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

            $('#removeModal').on('show.bs.modal', function(e) {
                $(this).find('.remove').attr('href', $(e.relatedTarget).data('href'));
            });
        });
    </script>
</body>
</html>