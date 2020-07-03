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
    echo Input::get('grade1');
}

if (Input::exists()) 
{
    $grade_ids = Input::get('grade_id');
    $grades = Input::get('grade');

    try 
    {
        foreach ($grade_ids as $id) 
        {
            $instructor->updateGrades(array(
                'grade' => $grades[$id]
            ), $id);
        }

        Session::flash('result', '<div class="alert alert-success">Student&apos;s has been updated successfully!</div>');
        Redirect::to($location);
    }
    catch (Exception $e) 
    {
        Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
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
                        <li class="breadcrumb-item"><a href="view-grades.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') : '') ?>"><i class="fas fa-table"></i> &nbsp;View Grades</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-edit"></i> &nbsp;Edit Grades</li>
                    </ol>
                </nav>
                <?php
                if (Input::exists('get')) 
                {
                    $get = DB::getInstance()->get('view_students', array('student_id', '=', Input::get('student')))->first();
                    $sub = DB::getInstance()->get('view_subjects', array('subject_id', '=', Input::get('subject')))->first();

                    $name = $get->firstname . ' ' . $get->lastname;
                    $code = $sub->subject_code;
                }
                ?>
                <h4>Edit Grades</h4>
                <?php echo (Session::exists('result') ? Session::flash('result') : '') ?>
                <hr>
                <span>Student Name: <strong><?php echo (isset($name) ? $name : '') ?></strong></span>
                <br>
                <span>Subject Code: <strong><?php echo (isset($code) ? $code : '') ?></strong></span>
                <br><br>
                <form action="" method="post">
                    <?php
                    if (Input::exists('get')) 
                    {
                        $sid = Input::get('student');
                        $subj = Input::get('subject');

                        $numOfCols = 2;
                        $rowCount = 0;
                        $bootstrapColWidth = 6 / $numOfCols;
                    ?>
                    <div class="row">
                    <?php
                        $get = DB::getInstance()->query("SELECT * FROM grades WHERE student_id = ? AND subject_id = ? ORDER BY grade_id ASC", array($sid, $subj));

                        if ($get->count()) 
                        {
                            foreach ($get->results() as $row) 
                            {
                    ?>
                        <div class="col-lg-<?php echo $bootstrapColWidth; ?> mb-2">
                            <input type="hidden" name="grade_id[<?php echo $row->grade_id ?>]" value="<?php echo $row->grade_id ?>">
                            <label for="term<?php echo $row->term ?>">Term: <strong><?php echo $row->term ?></strong></label>
                            <input type="text" class="form-control" id="term<?php echo $row->term ?>" name="grade[<?php echo $row->grade_id ?>]" value="<?php echo $row->grade ?>">
                        </div>
                    <?php
                                $rowCount++;
                                if ($rowCount % $numOfCols == 0) echo '</div><div class="row">';
                            }
                        }
                    }
                    ?>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                        </div>
                    </div>
                </form>
                <?php
                if (Input::exists()) 
                {
                    $grade_ids = Input::get('grade_id');
                    $grade = Input::get('grade');

                    foreach ($grade_ids as $gr) 
                    {
                        echo "Id: {$gr} | Grade: {$grade[$gr]}";    
                    }
                }
                ?>
                <hr>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php' ?>
</body>
</html>