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
    $subj = (Input::exists('get') ? Input::get('subject') : '');
    if (Input::get('btn-quiz')) 
    {
        try 
        {
            $instructor->addQuiz(array(
                'quiz_title' => Input::get('title'),
                'score' => Input::get('score'),
                'over' => Input::get('overscore'),
                'term' => Input::get('term'),
                'quiz_date' => Input::get('quiz_date'),
                'year_id' => Input::get('year__id'),
                'subject_id' => $subj,
                'student_id' => Input::get('student__id'),
                'instructor_id' => $data->instructor_id,
                'semester' => $semester,
                'school_year' => $school_year
            ));

            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Quiz</strong> has been added to student!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger mb-2">' . $e->getMessage() . '<br>' . $derp . '</div>');
            Redirect::to($location);
        }
        
    } 
    else if (Input::get('btn-rect')) 
    {
        try 
        {
            $instructor->addRecitation(array(
                'recitation_title' => Input::get('title'),
                'score' => Input::get('score'),
                'term' => Input::get('term'),
                'recitation_date' => Input::get('recitation_date'),
                'year_id' => Input::get('year__id'),
                'subject_id' => $subj,
                'student_id' => Input::get('student__id'),
                'instructor_id' => $data->instructor_id,
                'semester' => $semester,
                'school_year' => $school_year
            ));

            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Recitation</strong> has been added to student!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger mb-2">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }

    } 
    else if (Input::get('btn-exam')) 
    {
        try 
        {
            $instructor->addExam(array(
                'exam_title' => Input::get('title'),
                'score' => Input::get('score'),
                'over' => Input::get('overscore'),
                'term' => Input::get('term'),
                'exam_date' => Input::get('exam_date'),
                'year_id' => Input::get('year__id'),
                'subject_id' => $subj,
                'student_id' => Input::get('student__id'),
                'instructor_id' => $data->instructor_id,
                'semester' => $semester,
                'school_year' => $school_year
            ));

            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Exam</strong> has been added to student!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger mb-2">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    } 
    else if (Input::get('btn-grade')) 
    {
        try 
        {
            $instructor->addGrade(array(
                'term' => Input::get('term'),
                'grade' => Input::get('grade'),
                'grade_date' => Input::get('grade_date'),
                'year_id' => Input::get('year__id'),
                'subject_id' => $subj,
                'student_id' => Input::get('student__id'),
                'instructor_id' => $data->instructor_id,
                'semester' => $semester,
                'school_year' => $school_year
            ));

            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Grade</strong> has been added to student!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger mb-2">' . $e->getMessage() . '</div>');
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
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-users"></i> &nbsp;View Students</li>
                    </ol>
                </nav>
                <h4>View Students</h4>
                <hr>
                <div class="form-row">
                    <form action="" method="get">
                        <div class="form-row align-items-center">
                            <div class="col-auto mb-2">
                                <a role="button" href="attendance.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') : '')  ?>" class="btn btn-primary"><i class="fas fa-tasks"></i> &nbsp;Attendance</a>
                            </div>
                            <div class="col-sm-12 mb-2">
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
                
                <hr><br>
                <h3 class="text-center">
                    <?php 
                    if (Input::exists('get') && Input::get('subject') != 'default') {
                        $get = DB::getInstance()->query("SELECT * FROM view_instructor_subjects 
                                                         WHERE instructor_id = ? 
                                                            AND subject_id = ? 
                                                            AND semester = ? 
                                                            AND school_year = ?", array($data->instructor_id, Input::get('subject'), $semester, $school_year))->first();
                    
                        echo $get->subject_code . ': ' . $get->subject_name;
                    }
                    ?>        
                </h3>
                <hr>
                <?php echo (Session::exists('result') ? Session::flash('result') : '') ?>
                <br>
                <table class="" border="0" id="view_students_list">
                    <thead class="text-center">
                        <th>Student #</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </thead>
                    <tbody class="text-center">
                        <?php
                        if (Input::exists('get')) 
                        {
                            if (Input::get('subject') != 'default') 
                            {
                                if ($instructor->getStudentsBySubject(Input::get('subject'), $school_year, $semester)) 
                                {
                                    $count = 0;
                                    foreach ($instructor->results() as $row)
                                    {
                                        if ($count % 2 == 0)
                                        {
                                            echo '<tr class="row-gray bottom_border">';
                                        } 
                                        else 
                                        {
                                            echo '<tr class="bottom_border">';
                                        }
                        ?>
                            <td class="td-id c-green">
                                <input type="hidden" name="stud_id" value="<?php echo $row->student_id ?>">
                                <input type="hidden" name="year_id" value="<?php echo $row->year_id ?>">
                                <?php echo $row->student_no ?></td>

                            <td class="c-green">
                                <?php echo strtoupper($row->lastname) . ', ' . $row->firstname . ' ' . $row->middlename ?>
                            </td>

                            <td class="td-actions c-green">
                                <a class="btn btn-primary btn-sm mb-1 mini" data-toggle="tooltip" title="View Attendance" data-placement="bottom" href="view-attendance.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') . '&student=' . $row->student_id . '' : '') ?>" >
                                    <i class="fas fa-table"></i>
                                </a>
                                
                                <a role="button" class="btn btn-info btn-sm text-light mb-1 mini" data-toggle="tooltip" href="view-quiz-recitation-exam.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') . '&student=' . $row->student_id . '&year=' . $row->year_id : '') ?>" title="View Quizzes|Recitations|Exams" data-placement="bottom">
                                    <i class="fas fa-table"></i>
                                </a>
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
                </table>
                <br><br><br><br>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php'; ?>
</body>
</html>