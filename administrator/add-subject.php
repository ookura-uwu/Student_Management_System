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


if (Input::exists()) 
{
    if (Token::check(Input::get('token'))) 
    {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'subject_code' => array(
                'name' => 'Subject Code',
                'required' => true,
                'min' => 3
            ),
            'subject_name' => array(
                'name' => 'Subject Name',
                'required' => true,
                'min' => 3
            ),
            'days' => array(
                'name' => 'Days',
                'required' => true,
                'min' => 3
            ),
            'class_start' => array(
                'name' => 'Class Starts',
                'required' => true
            ),
            'class_end' => array(
                'name' => 'Class Ends',
                'required' => true
            ),
            'program' => array(
                'name' => 'Program',
                'required' => true
            ),
            'section' => array(
                'name' => 'Section',
                'required' => true
            )
        ));

        if ($validate->passed()) 
        {
            try 
            {

                $student->addSubject(array(
                    'subject_code' => Input::get('subject_code'),
                    'subject_name' => Input::get('subject_name'),
                    'class_days' => Input::get('days'),
                    'class_starts' => Input::get('class_start') . Input::get('class_start_hr'),
                    'class_ends' => Input::get('class_end') . Input::get('class_end_hr'),
                    'section_id' => Input::get('section'),
                    'units' => Input::get('units'),
                    'program' => Input::get('program'),
                    'semester' => $semester,
                    'school_year' => $school_year
                ));

                Session::flash('result', '<div class="alert alert-success">New Subject has been added successfully!</div>');
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
            if (Input::get('days') == '') 
            {
                $errors .= 'Class Days should have at least 1 selected day';
            }

            foreach($validate->errors() as $error) 
            {
                $errors .= $error . '<br>';
            }

            Session::flash('result', '<div class="alert alert-danger">' . $errors . '</div>');
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
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-plus-square"></i> &nbsp;Add Subject</li>
                    </ol>
                </nav>
                <?php (Session::exists('result') ? print Session::flash('result') : '') ?>
                <h4>Add Subject</h4>
                <hr>
                <form action="" method="post">
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Subject Code:</span>
                                </div>
                                <input type="text" class="form-control" name="subject_code" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Subject Name:</span>
                                </div>
                                <input type="text" class="form-control" name="subject_name" required>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-12 col-md-6 mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Subject Units:</span>
                                </div>
                                <select class="custom-select" name="units">
                                    <option value="">Select Units</option>
                                    <option value="--">--</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-6">
                            <h6>Schedule</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-3 col-lg-4 col-md-5 mb-3">
                            <label>Days:</label><br>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Mon">
                                        <label class="custom-control-label" id="monday" for="Mon">Mon</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Tue">
                                        <label class="custom-control-label" id="tuesday" for="Tue">Tue</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Wed">
                                        <label class="custom-control-label" id="wednesday" for="Wed">Wed</label>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Thu">
                                        <label class="custom-control-label" id="thursday" for="Thu">Thu</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Fri">
                                        <label class="custom-control-label" id="friday" for="Fri">Fri</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input" id="Sat">
                                        <label class="custom-control-label" id="saturday" for="Sat">Sat</label>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <label>Selected Days: <strong class="" id="days">--</strong></label>
                            <input type="hidden" class="border-0" name="days">

                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-7 mb-3">
                            <label>Class Time (Start - End):</label>
                            <div class="form-row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <input type="text" class="form-control" name="class_start" id="class_start" onclick="$class_start.open();" readonly>
                                        </div>
                                        <select class="custom-select" name="class_start_hr">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <input type="text" class="form-control" name="class_end" id="class_end" onclick="$class_end.open();" readonly>
                                        </div>
                                        <select class="custom-select" name="class_end_hr">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-6">
                            <hr>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-xl-4 col-lg-6 col-md-8 mb-3">
                            <label for="program">Program:</label>
                            <select class="form-control" id="program" width="100%" name="program">
                                <option value="">Select Program</option>
                                <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                                <option value="Bachelor of Technical Teacher Education">Bachelor of Technical Teacher Education</option>
                                <option value="Bachelor of Technical Vocational Teacher Education">Bachelor of Technical Vocational Teacher Education</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xl-4 col-lg-4 col-md-8 mb-3">
                            <label for="sections">Section:</label>
                            <select class="form-control" id="sections" name="section">
                                <option value="">Select Section</option>
                                <?php
                                if ($student->getSections()) 
                                {
                                    foreach($student->results() as $row) 
                                    {
                                ?>
                                <option value="<?php echo $row->section_id ?>"><?php echo $row->section_name ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br><br><br>
                    <div class="form-row">
                        <div class="col-lg-3 col-md-4">
                            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                            <button type="submit" class="btn btn-primary col-sm-12" id="btn_save_subject">Save</button>
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
            // Dropdowns
            // $('#program').dropdown();
            // $('#year').dropdown();
            // $('#semester').dropdown();

            // Class Time
            $class_start = $('#class_start').timepicker({ format: 'hh:MM' });
            $class_end = $('#class_end').timepicker({ format: 'hh:MM' });

            // Selected Days
            $(document).ready(displayCheckbox);

            countSelectedCB = [];

            function displayCheckbox() {
                $("input:checkbox").change(function() {
                    selectedCB = [];
                    notSelectedCB = [];

                    countSelectedCB.length = 0; // clear selected count
                    $("input:checkbox").each(function() {
                        if ($(this).is(":checked")) {
                            countSelectedCB.push($(this).attr("id"));
                        }
                    });

                    $('input[name=days]').val(countSelectedCB.join("/"));
                    document.getElementById('days').innerHTML = countSelectedCB.join('/');
                });
            }
        });
    </script>
</body>
</html>