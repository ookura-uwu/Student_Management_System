<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();

$location = 'index.php';

$semester = (isset($semester) ? $semester : '');
$school_year = (isset($school_year) ? $school_year : '');

// Redirect if session expires or not logged in
if (($user->isLoggedIn() && !$user->hasPermission('Administrator')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

// Save New School Year And Semester -- Modal Form -> navbar.php
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

// Use Previous School Year and Semester -- Form -> navbar.php
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
            $student->updateSchoolYear(array('isCurrent' => 1), $id); // Update SY isCurrent to 1, to use selected SY and Sem

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
                
                <div class="dashboard">
                    <hr>
                    <div class="card-group">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary">
                                <h5 class="text-center"><i class="fas fa-users text-light" style="font-size: 50px;"></i></h5>
                            </div>
                            <div class="card-body">
                                <h1 class="text-center">
                                    <?php
                                    $get = DB::getInstance()->query("SELECT * FROM view_semestral_instructors WHERE semester = ? AND school_year = ?", array($semester, $school_year));

                                    $count = 0;
                                    if ($get->count()) 
                                    {
                                        $count = $get->count();
                                        echo $count;
                                    } 
                                    else 
                                    {
                                        echo $count;
                                    }
                                    ?>
                                </h1>
                                <br>
                                <h5 class="text-center">Instructors</h5>
                                <hr>
                                <div class="text-center">
                                    <a class="btn btn-link" href="instructors-list.php">Go to Instructors <i class="fas fa-arrow-right"></i></a>
                                    <a class="btn btn-link" href="instructor.php?action=add">Add Instructor <i class="fas fa-plus"></i></a>
                                    <a class="btn btn-link" href="assign-subjects_instructor.php">Assign Subjects to Instructor <i class="fas fa-file-alt"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-dark">
                                <h5 class="text-center"><i class="fas fa-user-graduate text-light" style="font-size: 50px;"></i></h5>
                            </div>
                            <div class="card-body">
                                <h1 class="text-center">
                                    <?php
                                    $get = DB::getInstance()->query("SELECT * FROM view_semestral_students WHERE semester = ? AND school_year = ?", array($semester, $school_year));
                                    $count = 0;
                                    if ($get->count()) 
                                    {
                                        $count = $get->count();
                                        echo $count;
                                    } else {
                                        echo $count;
                                    }
                                    ?>
                                </h1>
                                <h5 class="text-center">Currently Enrolled</h5>
                                    <?php
                                    $get = DB::getInstance()->query("SELECT * FROM view_students");

                                    $count = 0;

                                    if ($get->count()) 
                                    {
                                        $count = $get->count();
                                    }
                                    ?>
                                    <h6 class="text-center">Total Students: <?php echo $count ?></h6>
                                <hr>
                                <div class="text-center">
                                    <a class="btn btn-link" href="students.php">Go to Students List <i class="fas fa-arrow-right"></i></a>
                                    <a class="btn btn-link" href="student.php?action=add">Add Student <i class="fas fa-plus"></i></a>
                                    <a class="btn btn-link" href="assign-subjects.php">Assign Subjects to Students <i class="fas fa-file-alt"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-secondary">
                                <h5 class="text-center"><i class="fas fa-user-secret text-light" style="font-size: 50px;"></i></h5>
                            </div>
                            <div class="card-body">
                                <h1 class="text-center">
                                    <?php
                                    $get = DB::getInstance()->query("SELECT DISTINCT user_id FROM users WHERE group_id = 3");

                                    $count = 0;

                                    if ($get->count()) 
                                    {
                                        $count = $get->count();
                                        echo $count;
                                    } 
                                    else 
                                    {
                                        echo $count;
                                    }
                                    ?>
                                </h1><br>
                                <h5 class="text-center">Administrator</h5>
                                <hr>
                                <div class="text-center">
                                    <a class="btn btn-link" href="view-admins.php">Go to Administrators <i class="fas fa-arrow-right"></i></a>
                                    <a class="btn btn-link" href="add-admin.php">Add Administrator <i class="fas fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                </div>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>
    
    <?php include 'jsplugins.php' ?>
</body>
</html