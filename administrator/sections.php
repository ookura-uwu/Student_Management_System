
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
            'section_name' => array(
                'name' => 'Section Name', 
                'required' => true, 
                'unique' => 'sections', 
                'min' => 3
            ),
            'year' => array(
                'name' => 'Year',
                'required' => true
            ),
            'program' => array(
                'name' => 'Program',
                'required' => true
            )
        ));

        if ($validate->passed()) 
        {
            try 
            {
                
                $student->addSection(array(
                    'section_name' => Input::get('section_name'),
                    'program' => Input::get('program'),
                    'year' => Input::get('year')
                ));

                Session::flash('result', '<div class="alert alert-success">New Section has been added successfully!</div>');
                Redirect::to('sections.php');

            } 
            catch (Exception $e) 
            {
                Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
                Redirect::to('sections.php');
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
            Redirect::to('sections.php');
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
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Sections</li>
                    </ol>
                </nav>
                <?php
                if (Session::exists('result')) 
                {
                    echo Session::flash('result');
                }
                ?>
                <h4>Sections</h4>
                <hr>
                <h6>
                    <button class="btn btn-primary" data-toggle="collapse" data-target="#collapseAddSection" aria-expanded="false" aria-controls="collapseAddSection">Add Section</button>
                    <a role="button" class="btn btn-primary" href="add-students-to-section.php">Add Students to Section</a>
                </h6>
                <div class="collapse" id="collapseAddSection">
                    <hr>
                    <form action="" method="post" data-toggle="validator">
                        <div class="form-row align-items-center">
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 my-1">
                                <label class="sr-only" for="inlineSectionName">Section Name:</label>
                                <input type="text" class="form-control" name="section_name" id="inlineSectionName" placeholder="Section Name" required>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 my-1">
                                <label class="sr-only" for="inlineYear">Year:</label>
                                <select class="custom-select" id="inlineYear" width="100%" name="year" required>
                                    <option value="default">Select Year</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 my-1">
                                <label class="sr-only" for="inlineProgram">Program:</label>
                                <select class="custom-select" id="inlineProgram" width="100%" name="program" required>
                                    <option value="default">Select Program</option>
                                    <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                                    <option value="Bachelor of Technical Teacher Education">Bachelor of Technical Teacher Education</option>
                                    <option value="Bachelor of Technical Vocational Teacher Education">Bachelor of Technical Vocational Teacher Education</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 my-1">
                                <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                                <button type="submit" class="btn btn-primary col-sm-12">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <br>
                <h6>Secionts List</h6>
                <hr>
                <div class="table-responsive">
                    <table border="0" id="sections_list">
                        <thead class="text-center">
                            <th>Section</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </thead>
                        <tbody class="text-center tdata">
                            <?php
                            if ($student->getSections()) 
                            {
                                $count = 0;
                                foreach($student->results() as $section) 
                                {
                                    if ($student->getTotalStudentsBySection($section->section_id, $school_year, $semester)) 
                                    {
                                        $count = $student->count();
                                        $count = (($count > 0) ? "({$count})" : '');
                                    }

                                    
                            ?>
                            <tr class="bottom_border">
                                <td class="c-green"><?php echo $section->section_name ?></td>
                                <td class="c-green"><?php echo $section->year ?></td>
                                <td class="c-green"><a class="btn btn-link" href="view-list.php?section=<?php echo $section->section_id?>">View Students List <?php echo "{$count}" ?></a></td>
                            </tr>
                            <?php
                                    
                                }
                            }
                            ?>
                        </tbody>
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
            $('#sections_list').dataTable({
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });
            
        });
        
    </script>
</body>
</html>