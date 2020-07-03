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



if (Input::exists('get')) 
{
    if (is_numeric(Input::get('student'))) 
    {
        try
        {
            $student->removeStudentFromSection(Input::get('section'), Input::get('student'), $school_year, $semester);

            Session::flash('result', '<div class="alert alert-success">Selected student has been removed from section!</div>');
            Redirect::to('view-list.php?section=' . Input::get('section'));
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to('view-list.php?section=' . Input::get('section'));
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
                        <li class="breadcrumb-item"><a href="sections.php"><i class="fas fa-users"></i> &nbsp;Sections</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;List</li>
                    </ol>
                </nav>
                <?php
                echo (Session::exists('result') ? Session::flash('result') : '');
                ?>
                <h4>Current Students List</h4>
                <hr>
                <div class="container">
                    <div class="table-responsive">
                        <table border="0" id="v_current_list">
                            <thead class="text-center">
                                <th width="800">Student Name</th>
                                <th>Actions</th>
                            </thead>
                            <tbody class="text-center tdata">
                                <?php
                                if (Input::exists('get')) 
                                {
                                    if ($student->getCurrentSectionStudentsList(Input::get('section'), $school_year, $semester)) 
                                    {
                                        foreach ($student->results() as $row) 
                                        {
                                ?>
                                <tr class="">
                                    <td><?php echo $row->student_name ?></td>
                                    <td>
                                        <a class="btn btn-danger btn-sm text-light" data-href="?section=<?php echo Input::get('section'); ?>&student=<?php echo $row->student_id ?>" data-toggle="modal" data-target="#removeModal" style="background-color: #f33527">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                            <div class="modal fade" id="removeModal" tabindex="-1" role="document" aria-labelledby="removeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-crimson">
                                            <h6 class="text-light">Remove Student</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" class="text-light">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Selected student will be removed from <strong><?php echo $row->section_name ?></strong>, proceed?</label>
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
            $('#v_current_list').DataTable({ 'lengthMenu': [[10,25,50], [10,25,50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false });

            $('#removeModal').on('show.bs.modal', function(e) {
                $(this).find('.remove').attr('href', $(e.relatedTarget).data('href'));
            });
        });
    </script>
</body>
</html>