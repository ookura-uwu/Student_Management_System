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

// Use exising/past school year and semester
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

if (!Input::exists('get')) 
{
    Redirect::to('student.php?action=add');
}

// Save New Student Information
if (Input::exists()) 
{
    if (Token::check(Input::get('token_new_student'))) 
    {

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'student_no' => array(
                'name' => 'Student #',
                'required' => true,
                'unique' => 'students'
            ),
            'lastname' => array(
                'name' => 'Last Name',
                'required' => true,
                'min' => 3,
            ),
            'firstname' => array(
                'name' => 'First Name',
                'required' => true,
                'min' => 3,
            ),
            'program' => array(
                'name' => 'Program',
                'required' => true
            ),
            'year' => array(
                'name' => 'Year',
                'required' => true
            ),
            'password' => array(
                'name' => 'Password',
                'required' => true,
            ),
            'repassword' => array(
                'name' => 'Re-type Password',
                'required' => true,
                'matches' => 'password'
            )
        ));

        $salt = Hash::salt(32);

        if ($validate->passed()) 
        {        

            try 
            {

                $user->create(array(
                    'email' => Input::get('email'),
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'username' => Input::get('username'),
                    'password' => Hash::make(Input::get('password'), $salt),
                    'salt' => $salt,
                    'group_id' => 1
                ));

                $user_id = DB::getInstance()->get('users', array('username', '=', Input::get('username')))->first()->user_id;

                $student->create(array(
                    'student_no' => Input::get('student_no'),
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename'),
                    'gender' => Input::get('gender'),
                    'birthday' => Input::get('birthday'),
                    'address' => Input::get('address'),
                    'program' => Input::get('program'),
                    'year_id' => Input::get('year'),
                    'user_id' => $user_id
                ));

                $sid = DB::getInstance()->get('students', array('student_no', '=', Input::get('student_no')))->first()->student_id;

                $student->addStudentYear(array(
                    'student_id' => $sid,
                    'year_id' => Input::get('year'),
                    'semester' => $semester,
                    'school_year' => $school_year
                ));

                Session::flash('result', '<div class="alert alert-success">Student&apos;s Information has been added successfully!</div>');
                Redirect::to($location);

            } 
            catch (Exception $e) 
            {
                Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
                Redirect::to($location);
            }
        } 
        else 
        {
            $errors = '';
            foreach ($validate->errors() as $error) 
            {
                $errors .= $error . '<br>';
            }

            Session::flash('result', "<div class='alert alert-danger'>{$errors}</div>");
        }
    }
}

if (Input::exists('get')) 


{
    if (Input::get('action') == 'edit' && is_numeric(Input::get('id'))) 


    {
        $id = Input::get('id');

        $student = new Student();
        if ($student->getStudent($id)) 


        {
            $data = $student->data();
        }
    }
}

if (Input::exists()) 


{
    if (Token::check(Input::get('token_existing_student'))) 


    {
        $id = Input::get('student_id');
        $user_id = Input::get('user_id');

        try 


        {
            if (Input::get('password') != '' && Input::get('repassword') != '') 


            {
                $validate = new Validate();
                $validation = $validate->check($_POST, array(
                    'password' => array(
                        'name' => 'Password',
                        'min' => 6
                    ),
                    'repassword' => array(
                        'name' => 'Re-type Password',
                        'min' => 6,
                        'matches' => 'password'
                    )
                ));

                if ($validate->passed()) 


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
                    $errors = '';
                    foreach ($validate->errors() as $error) 


                    {
                        $errors .= $error . '<br>';
                    }

                    Session::flash('result', "<div class='alert alert-danger'>{$errors}</div>");
                    Redirect::to($location);
                }
            } 


            else 


            {
                $user->update(array(
                    'lastname' => Input::get('lastname'),
                    'firstname' => Input::get('firstname'),
                    'middlename' => Input::get('middlename')
                ), $user_id);
            }

            $student->update(array(
                'lastname' => Input::get('lastname'),
                'firstname' => Input::get('firstname'),
                'middlename' => Input::get('middlename'),
                'gender' => Input::get('gender'),
                'birthday' => Input::get('birthday'),
                'address' => Input::get('address'),
                'program' => Input::get('program'),
                'year_id' => Input::get('year')
            ), $id);

            if (!$student->checkStudentYear($id, Input::get('year'), $semester, $school_year)) 


            {
                $student->addStudentYear(array(
                    'student_id' => $id,
                    'year_id' => Input::get('year'),
                    'semester' => $semester,
                    'school_year' => $school_year
                ));
            }

            Session::flash('result', '<div class="alert alert-success">Student&apos;s Information has been updated successfully!</div>');
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
                        <li class="breadcrumb-item"><a href="students.php"><i class="fas fa-table"></i> &nbsp;Students Information</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-address-card"></i> &nbsp;
                            <?php 
                            if (Input::exists('get')) 

                            {
                                if (Input::get('action') == 'add') 

                                {
                                    echo 'Add Student Information';
                                } 

                                else if (Input::get('action') == 'edit') 

                                {
                                    echo 'Edit Student Information';
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
                            echo 'Add Student Information';
                        } 
                        else if (Input::get('action') == 'edit') 
                        {
                            echo 'Edit Student Information';
                        }
                    }
                    ?>    
                </h4>
                <hr>
                <form action="" method="post">
                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Basic Information</h6>
                            <hr>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-5 mb-3">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Student #:</span>
                                </div>
                                <input type="hidden" name="student_id" value="<?php (isset($data) ? print $data->student_id : '') ?>">
                                <input type="hidden" name="user_id" value="<?php (isset($data) ? print $data->user_id : '') ?>">

                                <input type="text" 
                                       class="form-control" 
                                       id="student_no" 
                                       tabindex="1" 
                                       name="student_no" 
                                       value="<?php (isset($data) ? print $data->student_no : print Input::get('student_no')) ?>" 
                                       <?php (Input::get('action') == 'edit' ? print 'readonly' : '') ?> 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="lastname">Last Name:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="lastname" 
                                   id="lastname" 
                                   tabindex="2" 
                                   onfocusout="user(this.value)" 
                                   value="<?php (isset($data) ? print $data->lastname : print Input::get('lastname')) ?>" 
                                   required>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="firstname">First Name:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="firstname" 
                                   id="firstname" 
                                   tabindex="3" 
                                   value="<?php (isset($data) ? print $data->firstname : print Input::get('firstname')) ?>" 
                                   required>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <label for="middlename">Middle Name:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="middlename" 
                                   id="middlename" 
                                   tabindex="4" 
                                   value="<?php (isset($data) ? print $data->middlename : Input::get('middlename')) ?>" >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-2 col-lg-4 col-md-12 mb-3">
                            <label for="gender">Gender:</label>
                            <select class="custom-select form-control-sm" name="gender" id="gender" tabindex="5">
                                <option value="">Select Gender</option>
                                <option value="Female" <?php (isset($data) ? ($data->gender == 'Female' ? print 'selected' : '') : (Input::get('gender') == 'Female' ? print 'selected' : '') ) ?>>Female</option>
                                <option value="Male" <?php (isset($data) ? ($data->gender == 'Male' ? print 'selected' : '') : (Input::get('gender') == 'Male' ? print 'selected' : '') ) ?>>Male</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-12 mb-3">
                            <label for="birthday">Birth Date:</label>
                            <input type="text" 
                                   name="birthday" 
                                   id="birthday" 
                                   tabindex="6" 
                                   value="<?php (isset($data) ? print $data->birthday : print Input::get('birthday')) ?>" 
                                   readonly>
                        </div>
                        <div class="col-xl-4 col-lg-8 col-md-12 mb-3">
                            <label for="address">Address:</label>
                            <textarea class="form-control form-control-sm" 
                                      id="address" 
                                      tabindex="7" 
                                      name="address"><?php (isset($data) ? print $data->address : print Input::get('address')) ?></textarea>
                        </div>
                    </div>

                    <div class= "form-row">
                        <div class="col-xl-6">
                            <hr>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Program Information</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-5 col-lg-6 col-md-9 mb-3">
                            <label for="program">Program:</label>
                            <select class="form-control form-control-sm" name="program" tabindex="8" id="program" width="100%" required>
                                <option value="">Select Program</option>
                                <option value="Bachelor of Science in Computer Science" 
                                <?php
                                if (isset($data)) 
                                {
                                    if ($data->program == 'Bachelor of Science in Computer Science')
                                        print 'selected';
                                    else
                                        print '';
                                } 
                                else 
                                {
                                    if (Input::get('program') == 'Bachelor of Science in Computer Science')
                                        print 'selected';
                                    else
                                        print '';
                                }
                                ?>>
                                    Bachelor of Science in Computer Science
                                </option>
                                <option value="Bachelor of Technical Teacher Education" 
                                <?php 
                                if (isset($data)) 
                                {
                                    if ($data->program == 'Bachelor of Technical Teacher Education')
                                        print 'selected';
                                    else
                                        print '';
                                } 
                                else 
                                {
                                    if (Input::get('program') == 'Bachelor of Technical Teacher Education')
                                        print 'selected';
                                    else
                                        print '';
                                }
                                ?>>
                                    Bachelor of Technical Teacher Education
                                </option>
                                <option value="Bachelor of Technical Vocational Teacher Education" 
                                <?php 
                                if (isset($data)) 
                                {
                                    if ($data->program == 'Bachelor of Technical Vocational Teacher Education')
                                        print 'selected';
                                    else
                                        print '';
                                } 
                                else 
                                {
                                    if (Input::get('program') == 'Bachelor of Technical Vocational Teacher Education')
                                        print 'selected';
                                    else
                                        print '';
                                }
                                ?>>
                                    Bachelor of Technical Vocational Teacher Education
                                </option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-3 mb-3">
                            <label for="year">Year:</label>
                            <select class="form-control form-control-sm" name="year" tabindex="9" id="year" width="100%" required>
                                <option value="">Select Year</option>
                            <?php
                            if ($student->getYears()) 
                            {
                                foreach($student->results() as $row) 
                                {
                            ?>
                                <option value="<?php echo $row->year_id ?>" <?php (isset($data) ? ($data->year == $row->year ? print 'selected' : '') : '' ) ?>><?php echo $row->year ?></option>
                            <?php
                                }
                            }
                            ?>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Account Information</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="col-xl-3 col-lg-4">
                            <label for="username">Username:</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="username" 
                                   id="username" 
                                   value="<?php (isset($data) ? print $data->username : print Input::get('username')) ?>" 
                                   readonly>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="col-xl-3 col-lg-4">
                            <label for="email">Email:</label>
                            <input type="text"
                                   class="form-control form-control-sm" 
                                   tabindex="9"
                                   name="email" 
                                   id="email" 
                                   value="<?php (isset($data) ? print $data->username : print Input::get('username')) ?>" >
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="col-xl-3 col-lg-4">
                            <label for="password">Password:</label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   tabindex="10" 
                                   name="password" 
                                   id="password" 
                                   <?php (Input::get('action') == 'edit' ? print '' : print 'required') ?>>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-xl-3 col-lg-4">
                            <label for="repassword">Re-type Password:</label>
                            <input type="password" 
                                   class="form-control form-control-sm" 
                                   tabindex="11" 
                                   name="repassword" 
                                   id="repassword" 
                                   <?php (Input::get('action') == 'edit' ? print '' : print 'required') ?>>
                        </div>
                    </div>

                    <br>
                    <div class="form-row">
                        <div class="col-lg-3 col-md-4">
                            <?php
                            if (Input::exists('get')) 
                            {
                                if (Input::get('action') == 'add') 
                                {
                                    echo '<input type="hidden" name="token_new_student" value="' . Token::generate() . '">';
                                    echo '<button type="submit" tabindex="12" class="btn btn-success col-sm-12" id="save_new_info">Save Information</button>';
                                } 
                                else if (Input::get('action') == 'edit') 
                                {
                                    echo '<input type="hidden" name="token_existing_student" value="' . Token::generate() . '">';
                                    echo '<button type="submit" tabindex="12" class="btn btn-primary col-sm-12" id="save_existing_info">Update Information</button>';
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#birthday').datepicker({
                todayHighlight: true,
                autoclose: true,
                format: 'yyyy-mm-dd',
                header: true, 
                modal: true, 
                footer: true
            });
        });

        function user(str) {
            var no = document.getElementById('student_no').value
            if (str != "" && no != "") 
            {
                str = str.substring(0, 1);
                str = str + "" + no;

                document.getElementById('username').value = str;
            }
        }
    </script>
</body>
</html>