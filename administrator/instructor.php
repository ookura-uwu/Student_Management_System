
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



if (!Input::exists('get') || Input::get('action') == '') 
{
    Redirect::to('instructor.php?action=add');
}

if (Input::exists()) 
{
    if (Token::check(Input::get('token_new_instructor'))) 
    {
        
        $validate = new Validate();
        
        $validation = $validate->check($_POST, array(
            'lastname' => array(
                'name' => 'Last Name',
                'required' => true,
                'min' => 3
            ),
            'firstname' => array(
                'name' => 'First Name',
                'required' => true,
                'min' => 3
            ),
            'username' => array(
                'name' => 'Username',
                'required' => true,
                'min' => 3,
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
            $salt = Hash::salt(32);

            try 
            {

                $user->create(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'username' => Input::get('username'),
                    'password' => Hash::make(Input::get('password'), $salt),
                    'salt' => $salt,
                    'group_id' => 2
                ));

                $user_id = DB::getInstance()->get('users', array('username', '=', Input::get('username')))->first()->user_id;

                $instructor->create(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'gender' => Input::get('gender'),
                    'user_id' => $user_id
                ));

                Session::flash('result', '<div class="alert alert-success">Instructor&apos;s Information has been added successfully!</div>');
                Redirect::to('instructor.php?action=add');

            } 
            catch (Exception $e) 
            {
                Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
                Redirect::to('instructor.php?action=add');
            }
        } 
        else 
        {
            $errors = '';
            foreach ($validate->errors() as $error) 
            {
                $errors .= $error . '<br>';
            }
            Session::flash('result', '<div class="alert alert-danger">' . $errors . '</div>');
        }
    }
}

if (Input::exists('get')) 
{
    if (Input::get('action') == 'edit' && is_numeric(Input::get('instructor'))) 
    {
        $id = Input::get('instructor');

        if ($instructor->getInstructor($id)) 
        {
            $data = $instructor->data();
        }   
    }
}

if (Input::exists()) 
{
    if (Token::check(Input::get('token_existing_instructor'))) 
    {
        $id = Input::get('instructor_id');
        $user_id = Input::get('user_id');

        try 
        {
            if (Input::get('password') != '') 
            {
                $salt = Hash::salt(32);
                
                $user->update(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'password' => Hash::make(Input::get('password'), $salt),
                    'salt' => $salt
                ), $user_id);
            } 
            else 
            {
                $user->update(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename')
                ), $user_id);
            }

            $instructor->update(array(
                'lastname' => Input::get('lastname'),
                'firstname' => Input::get('firstname'),
                'middlename' => Input::get('middlename')
            ), $id);

            Session::flash('result', '<div class="alert alert-success">Instructor&apos;s Information has been updated successfully!</div>');
            Redirect::to('instructor.php?action=edit&instructor=' . $id . '');
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to('instructor.php?action=edit&instructor=' . $id . '');
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
                        <li class="breadcrumb-item"><a href="instructors-list.php"><i class="fas fa-table"></i> &nbsp;Instructor Information</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-address-card"></i> &nbsp;
                            <?php 
                            if (Input::exists('get')) 
                            {
                                if (Input::get('action') == 'add') 
                                {
                                    echo 'Add Instructor Information';
                                } 
                                else if (Input::get('action') == 'edit') 
                                {
                                    echo 'Edit Instructor Information';
                                }
                            }
                            ?>
                        </li>
                    </ol>
                </nav>
                <?php
                if (Session::exists('result')) 
                {
                    echo Session::flash('result');
                }
                ?>
                <h4>
                    <?php 
                    if (Input::exists('get')) 
                    {
                        if (Input::get('action') == 'add') 
                        {
                            echo 'Add Instructor Information';
                        } 
                        else if (Input::get('action') == 'edit') 
                        {
                            echo 'Edit Instructor Information';
                        }
                    }
                    ?>
                </h4>
                <hr>
                <form action="" method="post" data-toggle="validator">
                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Basic Information</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-5 mb-3">
                            <div class="input-group">
                                <input type="hidden" name="instructor_id" value="<?php (isset($data) ? print $data->instructor_id : '') ?>">
                                <input type="hidden" name="user_id" value="<?php (isset($data) ? print $data->user_id : '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="lastname">Last Name:</label>
                            <input type="text" class="form-control" name="lastname" id="lastname" value="<?php (isset($data) ? print $data->lastname : '') ?>" required>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="firstname">First Name:</label>
                            <input type="text" class="form-control" name="firstname" id="firstname" value="<?php (isset($data) ? print $data->firstname : '') ?>" required>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="middlename">Middle Name:</label>
                            <input type="text" class="form-control" name="middlename" id="middlename" value="<?php (isset($data) ? print $data->middlename : '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Gender:</span>
                                </div>
                                <select class="custom-select" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Female" <?php echo (isset($data) ? (($data->gender == 'Female') ? 'selected' : '') : '') ?>>Female</option>
                                    <option value="Male" <?php echo (isset($data) ? (($data->gender == 'Male') ? 'selected' : '') : '') ?>>Male</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Account Information</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" name="username" id="username" value="<?php (isset($data) ? print $data->username : '') ?>" <?php (Input::get('action') == 'edit' ? print 'readonly' : print 'required') ?>>
                        </div>
                        <div class="col-xl-3 col-lg-4">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" name="password" id="password" <?php (Input::get('action') == 'edit' ? print '' : print 'required') ?>>
                        </div>
                        <div class="col-xl-3 col-lg-4">
                            <label for="repassword">Re-type Password:</label>
                            <input type="password" class="form-control" name="repassword" id="repassword" <?php (Input::get('action') == 'edit' ? print '' : print 'required') ?>>
                        </div>
                    </div>

                    <br><br><br>
                    <div class="form-row">
                        <div class="col-lg-3 col-md-4">
                            <?php
                            if (Input::exists('get')) 
                            {
                                if (Input::get('action') == 'add') 
                                {
                                    echo '<input type="hidden" name="token_new_instructor" value="' . Token::generate() . '">';
                                    echo '<button type="submit" class="btn btn-success col-sm-12">Save Information</button>';
                                } 
                                else if (Input::get('action') == 'edit') 
                                {
                                    echo '<input type="hidden" name="token_existing_instructor" value="' . Token::generate() . '">';
                                    echo '<button type="submit" class="btn btn-primary col-sm-12">Update Information</button>';
                                }
                            }
                            ?>
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
</body>
</html>