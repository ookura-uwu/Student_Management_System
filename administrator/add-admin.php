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
    if (Token::check(Input::get('token'))) 
    {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'firstname' => array(
                'name' => 'First Name',
                'required' => true,
                'min' => 3
            ),
            'lastname' => array(
                'name' => 'Last Name',
                'required' => true,
                'min' => 3
            ),
            'username' => array(
                'name' => 'Username',
                'required' => true,
                'min' => 6,
                'unique' => 'users'
            ),
            'password' => array(
                'name' => 'Password',
                'required' => true,
                'min' => 6
            ),
            'repassword' => array(
                'name' => 'Re-type Password',
                'required' => true,
                'matches' => 'password'
            )
        ));

        if ($validate->passed()) 
        {
            try 
            {
                $salt = Hash::salt(32);

                $user = new User();
                $user->create(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'username' => Input::get('username'),
                    'password' => Hash::make(Input::get('password'), $salt),
                    'salt' => $salt,
                    'group_id' => 3
                ));

                Session::flash('result', '<div class="alert alert-success">Administrator Information has been added successfully!</div>');
                Redirect::to('add-admin.php');
            } 
            catch (Exception $e) 
            {
                Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
                Redirect::to('add-admin.php');
            }
        } 
        else 
        {
            $errors = '';
            foreach($validate->errors() as $error)
                $errors .= $error . '<br>';

            Session::flash('result', "<div class='alert alert-danger'>{$errors}</div>");
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
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Users Management</li>
                        <li class="breadcrumb-item"><a href="view-admins.php"><i class="fas fa-table"></i> &nbsp;Administrators List</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-address-card"></i> &nbsp;Add Administrator</li>
                    </ol>
                </nav>
                <?php
                if (Session::exists('result')) {
                    echo Session::flash('result');
                }
                ?>
                <h4>Add Administrator</h4>
                <hr>
                <form action="" method="post">
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="lastname">Last Name:</label>
                            <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo Input::get('lastname') ?>" tabindex="1">
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="firstname">First Name:</label>
                            <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo Input::get('firstname') ?>" tabindex="2">
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="middlename">Middle Name:</label>
                            <input type="text" class="form-control" name="middlename" id="middlename" value="<?php echo Input::get('middlename') ?>" tabindex="3">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-8 col-md-6 mb-3">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" name="username" id="username" value="<?php echo Input::get('username') ?>" tabindex="4">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-8 col-md-6 mb-3">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" name="password" id="password" tabindex="5">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-8 col-md-6 mb-3">
                            <label for="repassword">Re-type Password:</label>
                            <input type="password" class="form-control" name="repassword" id="repassword" tabindex="5">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-8 col-md-6">
                            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                            <button type="submit" class="btn btn-primary btn-block" tabindex="6">Save Information</button>
                        </div>
                    </div>
                </form>
                <br><br><br>
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
            $('#birthday').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $('#program').dropdown();
            $('#year').dropdown();
        });
    </script>
</body>
</html>