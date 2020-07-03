<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

$location = basename($_SERVER['REQUEST_URI']);

if (($user->isLoggedIn() && !$user->hasPermission('Instructor')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

$instructor->getInstructorByUserId($user->data()->user_id);
$data = $instructor->data();

$behavior_grade = 0;
$avg = 0;
$quiz = 0;
$rec = 0;
$exam_gr = 0;
$att = 0;


if (Input::exists() && Input::get('save')) 
{
    try 
    {
        $addBehavior = DB::getInstance()->insert('student_behavior', array(
            'behavior' => Input::get('behavior'),
            'student_id' => Input::get('student'),
            'subject_id' => Input::get('subject'),
            'instructor_id' => $data->instructor_id,
            'year_id' => Input::get('year'),
            'semester' => $semester,
            'school_year' => $school_year
        ));

        Session::flash('result', '<div class="alert alert-success">Student Behavior has been added successfuly!</div>');
        Redirect::to($location);
    } 
    catch (Exception $e) 
    {
        Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
        Redirect::to($location);
    }
}

if (Input::exists() && Input::get('confirm')) 
{
    if (Input::get('category') == 'Recitation') 
    {
        try {
            $deleteRecitation = DB::getInstance()->delete('recitations', array('recitation_id', '=', Input::get('delete_id')));

            Session::flash('result', '<div class="alert alert-success">Recitation has been deleted successfuly!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    } 
    else if (Input::get('category') == 'Quiz') 
    {
        try 
        {
            $deleteRecitation = DB::getInstance()->delete('quizzes', array('quiz_id', '=', Input::get('delete_id')));

            Session::flash('result', '<div class="alert alert-success">Quiz has been deleted successfuly!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    } 
    else if (Input::get('category') == 'Exam') 
    {
        try 
        {
            $deleteRecitation = DB::getInstance()->delete('exams', array('exam_id', '=', Input::get('delete_id')));

            Session::flash('result', '<div class="alert alert-success">Exam has been deleted successfuly!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    }
}


if (Input::exists()) 
{
    $subj = (Input::exists('get') ? Input::get('subject') : '');
    $student_id = (Input::exists('get') ? Input::get('student') : '');
    $year = (Input::exists('get') ? Input::get('year') : '');
    $id = $data->instructor_id;

    if (Input::get('btn-quiz')) 
    {
        try 
        {
            $instructor->addQuiz(array(
                'quiz_title' => Input::get('title'),
                'score' => Input::get('score'),
                'term' => Input::get('term'),
                'quiz_date' => Input::get('quiz_date'),
                'year_id' => $year,
                'subject_id' => $subj,
                'student_id' => $student_id,
                'instructor_id' => $id,
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
                'year_id' => $year,
                'subject_id' => $subj,
                'student_id' => $student_id,
                'instructor_id' => $id,
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
                'term' => Input::get('term'),
                'exam_date' => Input::get('exam_date'),
                'year_id' => $year,
                'subject_id' => $subj,
                'student_id' => $student_id,
                'instructor_id' => $id,
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
}

if (Input::exists() && Input::get('confirm_save')) 
{
    $subj = (Input::exists('get') ? Input::get('subject') : '');
    $student_id = (Input::exists('get') ? Input::get('student') : '');
    $year = (Input::exists('get') ? Input::get('year') : '');
    $id = $data->instructor_id;
    try 
    {
        $save_grade = DB::getInstance()->insert('grades', array(
            'term' => Input::get('n_term'),
            'grade' => Input::get('n_grade'),
            'grade_date' => $time->format('Y-m-d'),
            'year_id' => $year,
            'student_id' => $student_id,
            'subject_id' => $subj,
            'instructor_id' => $id,
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
                        <li class="breadcrumb-item"><a href="view-students.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') : '')  ?>"><i class="fas fa-table"></i> &nbsp;View Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-users"></i> &nbsp;View Quizzes/Recitations/Exams</li>
                    </ol>
                </nav>

                <?php echo (Session::exists('result') ? Session::flash('result') : '') ?>

                <h4>View Quizzes/Recitations/Exams</h4>
                <hr>
                <?php
                if (Input::exists('get')) 
                {
                    $get = DB::getInstance()->get('students', array('student_id', '=', Input::get('student')))->first();
                    $subj = DB::getInstance()->get('subjects', array('subject_id', '=', Input::get('subject')))->first();

                    echo '<span>Student Name: <strong>' . $get->firstname . ' ' . $get->lastname . '</strong></span><br>';
                    echo '<span>Subject: <strong>' . $subj->subject_code . ': ' . $subj->subject_name . '</strong></span>';
                }
                ?>
                <hr>

                <div class="row mb-2">
                    <div class="col-xl-3 col-centered">
                        <form action="" method="get" onchange="this.form.submit()">
                            <input type="hidden" name="subject" value="<?php echo (Input::exists('get') ? Input::get('subject') : '') ?>">
                            <input type="hidden" name="student" value="<?php echo (Input::exists('get') ? Input::get('student') : '') ?>">
                            <input type="hidden" name="year" value="<?php echo (Input::exists('get') ? Input::get('year') : '') ?>">
                            <select class="form-control" name="term" onchange="this.form.submit()">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    $terms = array(0 => 'Prelim', 1 => 'Midterm', 2 => 'Semi', 3 => 'Finals');

                                    
                                    for ($i = 0; $i < count($terms); $i++) 
                                    {
                                        $count = 0;
                                        $getQTerm = DB::getInstance()->query("SELECT DISTINCT term FROM quizzes WHERE student_id = ? AND term = ?", array(Input::get('student'), $terms[$i]));

                                        if ($getQTerm->count()) 
                                        {
                                            $count += 1;
                                        }

                                        $getETerm = DB::getInstance()->query("SELECT DISTINCT term FROM exams WHERE student_id = ? AND term = ?", array(Input::get('student'), $terms[$i]));

                                        if ($getETerm->count()) 
                                        {
                                            $count += 1;
                                        }

                                        $getRTerm = DB::getInstance()->query("SELECT DISTINCT term FROM recitations WHERE student_id = ? AND term = ?", array(Input::get('student'), $terms[$i]));

                                        if ($getRTerm->count()) 
                                        {
                                            $count += 1;
                                        }

                                        if ($count > 0) 
                                        {
                                            echo "<option value='{$terms[$i]}' " . ($terms[$i] == (Input::exists('get') ? Input::get('term') : '') ? 'selected' : '') . ">$terms[$i]</option>";
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </form>

                    </div>
                </div>
                <?php
                if (Input::exists('get')) {
                    $check = DB::getInstance()->query("SELECT * FROM student_behavior WHERE student_id = ? 
                                                                                        AND instructor_id = ? 
                                                                                        AND subject_id = ? 
                                                                                        AND year_id = ?
                                                                                        AND semester = ? 
                                                                                        AND school_year = ?", array(Input::get('student'), $data->instructor_id, Input::get('subject'), Input::get('year'), $semester, $school_year));

                    if (!$check->count()) 
                    {
                        
                ?>
                <div class="row">
                    <div class="col-xl-6">
                        <form action="" method="post">
                        <div class="input-group col-centered">
                            <input type="text" class="form-control col-xl-4" name="behavior" placeholder="Input Behavior Grade">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary col-xl-auto" name="save" value="true">Save</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
                <?php
                    } 
                    else 
                    {
                        $behavior_grade = $check->first()->behavior;
                    }

                } 
                ?>
                <hr>
                <button type="button" class="btn btn-primary col-xl-2 col-sm-12" id="add_qre" data-toggle="modal" data-target="#quizModal">Add Quiz/Recitation/Exam</button>
                <div class="row">
                    <div class="col-xl-6 mb-4">
                        <hr>
                        <h4 class="text-center">Attendance</h4>
                        <hr>
                        <table class="c-green" id="attendance_table">
                            <thead class="text-center">
                                <th>Description</th>
                                <th>Total</th>
                            </thead>
                            <tbody class="tdata">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    $sid = Input::get('student');
                                    $subj = Input::get('subject');
                                    $year = Input::get('year');
                                    $iid = $data->instructor_id;

                                    $totalPresent = 0;
                                    $totalAbsent = 0;

                                    $countPresent = DB::getInstance()->query("SELECT COUNT(day) AS countPresent FROM attendance 
                                                                              WHERE description = 'Present'
                                                                                AND student_id = ?
                                                                                AND subject_id = ?
                                                                                AND instructor_id = ? 
                                                                                AND year_id = ?
                                                                                AND semester = ?
                                                                                AND school_year = ?", array($sid, $subj, $iid, $year, $semester, $school_year));
                                    if ($countPresent->count()) 
                                    {
                                        $totalPresent = $countPresent->first()->countPresent;
                                    }

                                    $countAbsent = DB::getInstance()->query("SELECT COUNT(day) AS countAbsent FROM attendance 
                                                                              WHERE description = 'Absent'
                                                                                AND student_id = ?
                                                                                AND subject_id = ?
                                                                                AND instructor_id = ? 
                                                                                AND year_id = ?
                                                                                AND semester = ?
                                                                                AND school_year = ?", array($sid, $subj, $iid, $year, $semester, $school_year));
                                    if ($countAbsent->count()) 
                                    {
                                        $totalAbsent = $countAbsent->first()->countAbsent;
                                    }

                                    if ($totalAbsent > 0 && $totalPresent > 0) 
                                    {
                                        $total = ($totalPresent / ($totalPresent + $totalAbsent))  * 100;
                                        $att = formatGrade(ceil(number_format((float)$total, 2, '.', '')));
                                        $AB = ($att + $behavior_grade) / 2;
                                    }
                                ?>
                                <tr>
                                    <td>Present</td>
                                    <td><?php echo $totalPresent ?></td>
                                </tr>
                                <tr>
                                    <td>Absent</td>
                                    <td><?php echo $totalAbsent ?></td>
                                </tr>
                                <?php
                                if ($behavior_grade > 0) {
                                ?>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $AB ?></strong></td>
                                </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-6 mb-4">
                        <hr>
                        <h4 class="text-center">Recitations</h4>
                        <hr>
                        <table id="recitations_list">
                            <thead class="text-center">
                                <th>Title</th>
                                <th>Scores</th>
                                <th>Term</th>
                            </thead>
                            <tbody class="text-center tdata">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    $sid = Input::get('student');
                                    $subj = Input::get('subject');
                                    $year = Input::get('year');
                                    
                                    if (!Input::get('term')) 
                                    {
                                        $term = 'Prelim';
                                    } 
                                    else 
                                    {
                                        $term = Input::get('term');
                                    }

                                    $getR = DB::getInstance()->query("SELECT * FROM recitations WHERE student_id = ? AND year_id = ? AND subject_id = ? AND instructor_id = ? AND term = ? ORDER BY recitation_title ASC", array($sid, $year, $subj, $data->instructor_id, $term));
                                    if ($getR->count()) 
                                    {
                                        $count = 0;
                                        $sum = 0;

                                        foreach ($getR->results() as $row) 
                                        {
                                            $sum += $row->score;
                                ?>
                                <tr class="c-green text-center recitation_tr">
                                    <td class="td-rec-title recitation_td">
                                        <input type="hidden" name="recitation_id" value="<?php echo $row->recitation_id ?>" id="recitation_id"><?php echo $row->recitation_title ?>
                                    </td>
                                    <td class="recitation_td">
                                        <?php echo $row->score ?>        
                                    </td>
                                    <td class="recitation_td">
                                        <?php echo $row->term ?>
                                    </td>
                                </tr>
                                <?php
                                            $count++;
                                        }

                                        if ($count > 0) 
                                        {
                                            $total = $sum / $count . '0';
                                            $rec = getAverage(number_format((float)$total, 2, '.', ''));
                                ?>
                                <tr class="c-green">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $rec ?></strong></td>
                                    <td></td>
                                </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 mb-4">
                        <hr>
                        <h4 class="text-center">Quizzes</h4>
                        <hr>
                        <table id="quizzess_list">
                            <thead class="text-center">
                                <th>Title</th>
                                <th>Scores</th>
                                <th>Term</th>
                            </thead>
                            <tbody class="text-center tdata">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    $sid = Input::get('student');
                                    $subj = Input::get('subject');
                                    $year = Input::get('year');
                                    
                                    if (!Input::get('term')) 
                                    {
                                        $term = 'Prelim';
                                    } 
                                    else 
                                    {
                                        $term = Input::get('term');
                                    }

                                    $getQ = DB::getInstance()->query("SELECT * FROM quizzes WHERE student_id = ? AND year_id = ? AND subject_id = ? AND instructor_id = ? AND term = ?", array($sid, $year, $subj, $data->instructor_id, $term));
                                    if ($getQ->count()) 
                                    {
                                        $count = 0;
                                        $sum = 0;

                                        foreach ($getQ->results() as $row) 
                                        {
                                            $sum += $row->score;
                                ?>
                                <tr class="c-green quiz_tr">
                                    <td class="td-quiz-title quiz_td"><input type="hidden" name="quiz_id" value="<?php echo $row->quiz_id ?>" id="quiz_id"><?php echo $row->quiz_title ?></td>
                                    <td class="quiz_td"><?php echo $row->score ?></td>
                                    <td class="quiz_td"><?php echo $row->term ?></td>
                                </tr>
                                <?php
                                            $count++;
                                        }

                                        if ($count > 0) 
                                        {
                                            $total = $sum / $count;
                                            $quiz = getAverage(number_format((float)$total, 2, '.', ''))
                                ?>
                                <tr class="c-green">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $quiz ?></strong></td>
                                    <td></td>
                                </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-6 mb-4">
                        <hr>
                        <h4 class="text-center">Exams</h4>
                        <hr>
                        <table id="examinations_list">
                            
                            <thead class="text-center">
                                <th>Title</th>
                                <th>Scores</th>
                                <th>Term</th>
                            </thead>
                            <tbody class="text-center tdata">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    $sid = Input::get('student');
                                    $subj = Input::get('subject');
                                    $year = Input::get('year');
                                    
                                    if (!Input::get('term')) 
                                    {
                                        $term = 'Prelim';
                                    } 
                                    else 
                                    {
                                        $term = Input::get('term');
                                    }

                                    $getE = DB::getInstance()->query("SELECT * FROM exams WHERE student_id = ? AND year_id = ? AND subject_id = ? AND instructor_id = ? AND term = ?", array($sid, $year, $subj, $data->instructor_id, $term));
                                    
                                    if ($getE->count()) 
                                    {
                                        foreach($getE->results() as $row) 
                                        {
                                            $title = '';
                                            if ("Prelim" == $row->term) 
                                            {
                                                $title = 'Prelim Exam';
                                            } 
                                            else if ("Midterm" == $row->term) 
                                            {
                                                $title = 'Midterm Exam';
                                            } 
                                            else if ("Semi" == $row->term) 
                                            {
                                                $title = 'SemiFinal Exam';
                                            } 
                                            else if ("Finals" == $row->term) 
                                            {
                                                $title = 'Final Exam';
                                            }

                                            $exam_gr = $row->score;
                                ?>
                                <tr class="c-green exam_tr">
                                    <td class="td-exam-title exam_td"><input type="hidden" name="exam_id" value="<?php echo $row->exam_id ?>" id="exam_id"><?php echo $title ?></td>
                                    <td class="exam_td"><?php echo $row->score ?></td>
                                    <td class="exam_td"><?php echo $row->term ?></td>
                                </tr>
                                <?php
                                        }

                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xl-4 col-centered">
                        <?php
                        if ($exam_gr > 0)
                        {
                            $classStanding = ($quiz + $rec + $AB) / 3;
                            $avg = ($classStanding + $exam_gr) / 2;

                            $check = DB::getInstance()->query("SELECT * FROM grades WHERE term = ? 
                                                                                    AND student_id = ? 
                                                                                    AND subject_id = ? 
                                                                                    AND instructor_id = ?
                                                                                    AND year_id = ?
                                                                                    AND semester = ?
                                                                                    AND school_year = ?", array($term, Input::get('student'), Input::get('subject'), $data->instructor_id, Input::get('year'), $semester, $school_year));

                        ?>
                        <form action="" method="post">
                            <input type="hidden" name="grade_term" id="grade_term" value="<?php echo $term ?>">
                            <input type="hidden" name="_grade" id="_grade" value="<?php echo getAverage(number_format((float)$avg, 2, '.', '')) ?>">

                            <h5 class="text-center">Average Grade: <?php echo getAverage(number_format((float)$avg, 2, '.', '')) ?></h5>
                            <hr>
                        <?php
                            if (!$check->count()) 
                            {
                                echo "<button type='button' class='btn btn-primary btn-block' id='btn_save_grade' name='save_grade'>Save {$term} Grade</button>";
                            }
                        ?>
                        </form>
                        <?php
                        
                        }
                        if (Input::exists() && Input::get('confirm_save')) 
                        {
                            echo Input::get('_grade');
                        }
                        ?>
                    </div>
                </div>
                <div class="modal fade" id="saveGradeModal" role="document">
                    <div class="modal-dialog" role="dialog">
                        <div class="modal-content">
                            <form action="" method="post">
                                <div class="modal-header">
                                    <h4><span id="header-title">Save Grade</span></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <span id="body-title">Save Grade? You can edit it later in <strong>View Grades</strong></span>
                                    <input type="hidden" name="n_grade" id="n_grade">
                                    <input type="hidden" name="n_term" id="n_term">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="confirm_save" value="true">Confirm</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deleteModal" role="document">
                    <div class="modal-dialog" role="dialog">
                        <div class="modal-content">
                            <form action="" method="post">
                                <div class="modal-header modal-header-crimson">
                                    <h4 class="text-light"><span id="header-title"></span></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-light">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <span id="body-title"></span>
                                    <input type="hidden" name="delete_id" id="delete_id">
                                    <input type="hidden" name="category" id="category">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="confirm" value="true">Confirm</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="quizModal" tabindex="-1" aria-hidden="true" role="document">
                    <div class="modal-dialog" role="dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-header-green">
                                <h5 class="text-light">Quiz|Recitation|Exam</h5>
                                <button type="button" class="close" aria-label="Close" id="close-qregModal" data-dismiss="modal"><span class="text-light">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <select class="custom-select" onchange="showCategory(this)">
                                    <option value="0">Select Category</option>
                                    <option value="1">Quiz</option>
                                    <option value="2">Recitation</option>
                                    <option value="3">Exam</option>
                                </select>
                                <hr>
                                <!-- Quiz -->
                                <div id="quiz" style="display: none;">
                                    <form action="" method="post">
                                        <input type="hidden" name="category" value="Quiz">
                                        <h6>Quiz</h6>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-6 mx-auto mb-1">
                                                <input type="text" class="form-control" name="title" placeholder="Quiz Title: (eg. Quiz #1)">
                                            </div>
                                            <div class="col-xl-6 mx-auto mb-1">
                                                <select class="custom-select" name="term" required>
                                                    <option value="">Select Term</option>
                                                    <option value="Prelim">Prelim</option>
                                                    <option value="Midterm">Midterm</option>
                                                    <option value="Semi">Semi</option>
                                                    <option value="Finals">Finals</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12 mx-auto mb-1">
                                                <input type="text" class="form-control" name="score" placeholder="Score (Grade)">
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12">
                                                <label for="quiz_date">Quiz Date</label>
                                                <input type="text" id="quiz_date" name="quiz_date" readonly>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12"><button type="submit" class="btn btn-primary btn-block" name="btn-quiz" value="Quiz">Save</button></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- Quiz -->

                                <!-- Recitation -->
                                <div id="recitation" style="display: none;">
                                    <form action="" method="post">
                                        <input type="hidden" name="category" value="Recitation">
                                        <h6>Recitation</h6>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-4 mx-auto mb-1">
                                                <input type="text" class="form-control" name="title" placeholder="Recitation Title: (eg. Recitation #1)">
                                            </div>
                                            <div class="col-xl-4 mx-auto mb-1">
                                                <select class="custom-select" name="term" required>
                                                    <option value="">Select Term</option>
                                                    <option value="Prelim">Prelim</option>
                                                    <option value="Midterm">Midterm</option>
                                                    <option value="Semi">Semi</option>
                                                    <option value="Finals">Finals</option>
                                                </select>
                                            </div>
                                            <div class="col-xl-4 mx-auto mb-1">
                                                <input type="text" class="form-control" name="score" placeholder="Score">
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12">
                                                <label for="recitation_date">Recitation Date</label>
                                                <input type="text" id="recitation_date" name="recitation_date" readonly>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12"><button type="submit" class="btn btn-primary btn-block" name="btn-rect" value="Recitation">Save</button></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- Recitation -->

                                <!-- Exam -->
                                <div id="exam" style="display: none;">
                                    <div id="exam_result"></div>
                                    <form action="" method="post">
                                        <input type="hidden" name="category" value="Exam">
                                        <h6>Exam</h6>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-6 mx-auto mb-1">
                                                <input type="text" class="form-control" name="title" placeholder="Exam Title" id="exam_title" readonly>
                                            </div>
                                            <div class="col-xl-6 mx-auto mb-1">
                                                <select class="custom-select" name="term" 
                                                        onchange='checkExam(this, 
                                                                            <?php echo json_encode(Input::get('student')) ?>, 
                                                                            <?php echo json_encode(Input::get('subject')) ?>, 
                                                                            <?php echo json_encode($data->instructor_id) ?>, 
                                                                            <?php echo json_encode(Input::get('year')) ?>)' 
                                                        required>
                                                    <option value="">Select Term</option>
                                                    <option value="Prelim">Prelim</option>
                                                    <option value="Midterm">Midterm</option>
                                                    <option value="Semi">Semi</option>
                                                    <option value="Finals">Finals</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12 mx-auto mb-1">
                                                <input type="text" class="form-control" name="score" placeholder="Score (Grade)">
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12">
                                                <label for="exam_date">Exam Date</label>
                                                <input type="text" id="exam_date" name="exam_date" readonly>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-xl-12"><button type="submit" class="btn btn-primary btn-block" name="btn-exam" id="btn-exam" value="Examination">Save</button></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- Exam -->
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php' ?>
    <script type="text/javascript">
        $(document).ready(function(){

            // Delete function
            $('.recitation_tr').click(function(){
                $('#deleteModal').modal('toggle');

                var row = $(this).closest('tr');
                var title = row.find('.td-rec-title').text();
                var value = row.find('input[name=recitation_id]').val();

                title = "Delete <strong>" + title + "</strong>?";

                $('#deleteModal #header-title').text("Delete Recitation");
                $('#deleteModal #body-title').html(title);
                $('#deleteModal #delete_id').val(value);
                $('#deleteModal #category').val('Recitation');
            });

            $('.quiz_tr').click(function(){
                $('#deleteModal').modal('toggle');

                var row = $(this).closest('tr');
                var title = row.find('.td-quiz-title').text();
                var value = row.find('input[name=quiz_id]').val();

                title = "Delete <strong>" + title + "</strong>?";

                $('#deleteModal #header-title').text("Delete Quiz");
                $('#deleteModal #body-title').html(title);
                $('#deleteModal #delete_id').val(value);
                $('#deleteModal #category').val('Quiz');
            });

            $('.exam_td').click(function(){
                $('#deleteModal').modal('toggle');

                var row = $(this).closest('tr');
                var title = row.find('.td-exam-title').text();
                var value = row.find('input[name=exam_id]').val();

                title = "Delete <strong>" + title + "</strong>?";

                $('#deleteModal #header-title').text("Delete Exam");
                $('#deleteModal #body-title').html(title);
                $('#deleteModal #delete_id').val(value);
                $('#deleteModal #category').val('Exam');
            });
            // Delete Function

            // Save Grade
            $('#btn_save_grade').click(function(){
                $('#saveGradeModal').modal('toggle');

                var term = document.getElementById('grade_term').value;
                var grade = document.getElementById('_grade').value;

                document.getElementById('n_grade').value = grade;
                document.getElementById('n_term').value = term;
            });
            //

            $('#quiz_date').datepicker({ 
                showOtherMonths: true,
                todayHighlight: true,
                autoclose: true,
                format: 'yyyy-mm-dd',
                header: true, 
                modal: true, 
                footer: true 
            });
            $('#recitation_date').datepicker({ 
                showOtherMonths: true,
                todayHighlight: true,
                autoclose: true,
                format: 'yyyy-mm-dd',
                header: true, 
                modal: true, 
                footer: true
            });
            $('#exam_date').datepicker({ 
                showOtherMonths: true,
                todayHighlight: true,
                autoclose: true,
                format: 'yyyy-mm-dd',
                header: true, 
                modal: true, 
                footer: true
            });
        });
        function showCategory(elem) 
        {
            if (elem.value == 1) {
                $('#exam').hide('slow');
                $('#recitation').hide('slow');
                $('#quiz').show('slow');
            } 
            else if (elem.value == 2) 
            {
                $('#exam').hide('slow');
                $('#quiz').hide('slow');
                $('#recitation').show('slow')
            } 
            else if (elem.value == 3) 
            {
                $('#quiz').hide('slow');
                $('#recitation').hide('slow');
                $('#exam').show('slow')
            }

            $('#close-qregModal').click(function() {
                document.getElementById('result').innerHTML = "";
                document.getElementById('btn-grade').disabled = false;
                document.getElementById('term').value = '';
            });
        }

        function checkExam(term, id, sid, iid, yr) {
            if (term.value == "") 
            {
                document.getElementById('exam_result').innerHTML = "";
                return;
            }

            document.getElementById('exam_title').value = term.value + " Exam";

            if (window.XMLHttpRequest) 
            {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } 
            else 
            {
                // IE6 IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) 
                {
                    document.getElementById('exam_result').innerHTML = this.responseText;

                    var result = this.responseText;
                    if (result == "") 
                    {
                        document.getElementById('btn-exam').disabled = false;
                    } 
                    else 
                    {
                        document.getElementById('btn-exam').disabled = true;
                    }

                }
            }

            xmlhttp.open("GET", "checkExam.php?term=" + term.value + "&student=" + id + "&subject=" + sid + "&instructor=" + iid + "&year=" + yr);
            xmlhttp.send();
        }
    </script>
</body>
</html
<?php
function formatGrade($grade) {
    if ($grade >= 99 || $grade == 100) 
    {
        return '1.00';
    } 
    else if ($grade >= 96 || $grade == 98) 
    {
        return '1.25';
    } 
    else if ($grade >= 93 || $grade == 95) 
    {
        return '1.50';
    } 
    else if ($grade >= 90 || $grade == 92) 
    {
        return '1.75';
    } 
    else if ($grade >= 87 || $grade == 89) 
    {
        return '2.00';
    } 
    else if ($grade >= 84 || $grade == 86) 
    {
        return '2.25';
    } 
    else if ($grade >= 81 || $grade == 83) 
    {
        return '2.50';
    } 
    else if ($grade >= 78 || $grade == 80) 
    {
        return '2.75';
    } 
    else if ($grade >= 75 || $grade == 77) 
    {
        return '3.00';
    } 
    else 
    {
        return '5.00';
    }
}

function getAverage($avg ) {
    $whole = floor($avg);
    $fraction = $avg - $whole;
    $fraction = str_replace('.', '', number_format((float)$fraction, 2));

    if ($whole == 1 || $whole == 2) 
    {
        if ($fraction < 9) 
        {
            $result = $whole . '.00';
            return $result;
        } 
        else if ($fraction > 10 && $fraction < 30) 
        {
            $result = $whole . '.25';
            return $result;
        } 
        else if ($fraction > 31 && $fraction < 60) 
        {
            $result = $whole . '.50';
            return $result;
        } 
        else if ($fraction > 61 && $fraction < 80) 
        {
            $result = $whole . '.75';
            return $result;
        } 
        else if ($fraction >= 84 && $fraction <= 99) 
        {
            if ($whole == 1 || $whole == 2) 
            {
                $result = $whole + 1 . '.00';
                return $result;
            }
        }
    } 
    else if ($whole == 3 || $whole == 1) 
    {
        if (($fraction < 9) || ($fraction > 10 && $fraction < 30) || ($fraction > 31 && $fraction < 60) || ($fraction > 61 && $fraction <= 99)) 
        {
            $result = '5.00';
            return $result;
        } 
        else if ($whole == 4 || $whole == 5) 
        {
            $result = $whole . '.00';
            return $result;
        }
    }
}   