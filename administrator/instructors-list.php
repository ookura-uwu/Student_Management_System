<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

$location = basename($_SERVER['REQUEST_URI']);

$semester = (isset($semester) ? $semester : '');
$school_year = (isset($school_year) ? $school_year : '');

if (($user->isLoggedIn() && !$user->hasPermission('Administrator')) || (!$user->isLoggedIn())) {
    Redirect::to('../');
}

if (Input::exists() && Input::get('save_sy')) {
    if (!$student->checkSchoolYear(Input::get('school_year'), Input::get('semester'))) {
        try {
            (isset($sy_id) ? $student->updateSchoolYear(array('isCurrent' => 0), $sy_id) : '');

            $student->editSchoolYear(array(
                'schoolyear' => Input::get('school_year'),
                'semester' => Input::get('semester'),
                'isCurrent' => 1
            ));
            Session::flash('edit_sy_result', '<div class="alert alert-success">School Year and Semester has been updated successfully!</div>');
            Redirect::to($location);

        } catch (Exception $e) {
            Session::flash('edit_sy_result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
            Redirect::to($location);
        }
    }
}

if (Input::exists() && Input::get('use_existing')) {
    try {
        $sem = Input::get('existingSem');
        $sy = Input::get('existingSY');

        $checkAndGet = DB::getInstance()->query("SELECT * FROM school_year WHERE semester = ? AND schoolyear = ?", array($sem, $sy));

        if ($checkAndGet->count()){
            $id = $checkAndGet->first()->sy_id;

            $student->updateSchoolYear(array('isCurrent' => 0), $sy_id);
            $student->updateSchoolYear(array('isCurrent' => 1), $id);

            Session::flash('edit_sy_result', '<div class="alert alert-success">School Year settings has been changed successfully!</div>');
            Redirect::to($location);
        }
    } catch (Exception $e) {
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
                
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Instructors</li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Instructors Information</li>
                    </ol>
                </nav>
                <?php echo (Session::exists('result')) ? Session::flash('result') : ''; ?>
                <h4>Instructor Information</h4>

                <hr>
                <a role="button" href="instructor.php?action=add" class="btn btn-primary"><i class="fas fa-address-card"></i> &nbsp;Add Instructor</a>
                <a role="button" href="assign-subjects_instructor.php" class="btn btn-primary"><i class="fas fa-file-alt"></i>&nbsp;Assign Subjects</a>
                <br><br><br>
                <table id="instructors_list">
                    <thead>
                        <th>Name</th>
                        <th>Username</th>
                        <th class="text-center">Actions</th>
                    </thead>
                    <tbody class="tdata">
                        <?php
                        if ($instructor->getInstructors()) {
                            foreach($instructor->results() as $row) {
                                $result = '';
                                if ($instructor->getTotalSubjectsByInstructor($row->instructor_id, $semester, $school_year)) {
                                    $result = '[' . $instructor->count() . ']';
                                }
                        ?>
                        <tr class="c-green">
                            <td class="td_lastname"><input type="hidden" name="instr_id" id="instr_id"><?php echo strtoupper($row->lastname) . ', ' . $row->firstname . ' ' . $row->middlename ?></td>
                            <td class="td_username"><?php echo $row->username ?></td>
                            <td class="text-center">
                                <a role="button" class="btn btn-primary btn-sm update mb-1" data-toggle="tooltip" title="Update Information" data-placement="bottom" href="instructor.php?action=edit&instructor=<?php echo $row->instructor_id ?>"><i class="fas fa-edit"></i></a>
                                <a role="button" class="btn btn-secondary btn-sm mb-1" data-toggle="tooltip" title="View Subjects <?php echo $result     ?>" data-placement="bottom" href="view-subjects-list.php?instructor=<?php echo $row->instructor_id ?>&type=instructor"><i class="fas fa-file-alt"></i></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }   
                        ?>
                    </tbody>
                </table>                
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
            $('#instructors_list').DataTable({ 'lengthMenu': [[5,10,25,50] , [5,10,25,50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false 
            });

            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>
</body>
</html>