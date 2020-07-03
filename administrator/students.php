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


if (Input::exists() && Input::get('remove')) 
{
    try 
    {
        $removeSInfo = DB::getInstance()->query("DELETE FROM students WHERE student_id = ?", array(Input::get('student_id')));
        $removeUInfo = DB::getInstance()->query("DELETE FROM users WHERE user_id = ?", array(Input::get('user_id')));

        Session::flash('result', '<div class="alert alert-success">Student Information has been deleted successfully!</div>');
        Redirect::to($location);
    } 
    catch (Exception $e)
    {
        Session::flash('result', '<div class="alert alert-danger">' . $get->Message() . '</div>');
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
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Students</li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Students Information</li>
                    </ol>
                </nav>

                <h4>Students Information</h4>
                <hr>

                <?php 
                // Display result of appending students to selected section
                echo (Session::exists('result') ? Session::flash('result') : '');
                ?>

                <a role="button" href="student.php?action=add" class="btn btn-primary"><i class="fas fa-address-card"></i> &nbsp;Add Student</a>
                <hr>
                <br>

                <table class="" cellspacing="0" border="0" id="students_list" width="100%">
                    <thead>
                        <th>Student #</th>
                        <th>Name</th>
                        <th></th>
                    </thead>
                    <tbody class="tdata"></tbody>
                </table>
                <div class="modal fade" id="removeModal" role="document">
                    <div class="modal-dialog" role="dialog">
                        <div class="modal-content">
                            <form action="" method="post">
                                <div class="modal-header modal-header-crimson">
                                    <h4 class="text-light">Remove Student Information?</h4>
                                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span class="text-light">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <span id="stud_name"></span><br>
                                    <span>Deleting information will also delete all related records with this information (including: Class Records, Exams, and Grades), continue?</span>
                                    <input type="hidden" name="student_id" id="stud_id">
                                    <input type="hidden" name="user_id" id="uid">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger" name="remove" value="true">Remove</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
            var table = $('#students_list').DataTable({
                "autoWidth": true,
                "ajax": {
                    url: "students-data.php",
                    type: "post"
                },
                "columns": [
                    { 
                        "className": "details-control",
                        "data": "student_no"
                    },
                    { 
                        "className": "td-name details-control",
                        "data": "name"
                    },
                    {
                        "data": "delete"
                    }
                ],
                "order": [[1, "asc"]],
                "lengthMenu": [[10, 25, 50], [10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bAutoWidth": false
            });

            // Add event listener for opening and closing details
            $('#students_list tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) 
                {
                    // This row is already open - close it
                    $('div.slider', row.child()).slideUp(function(){
                        row.child.hide();
                        tr.removeClass('shown');
                    });

                } 
                else 
                {
                    // Open this row
                    row.child(students(row.data()), 'no-padding').show();
                    tr.addClass('shown');

                    $('div.slider', row.child()).slideDown();
                }
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('#students_list tbody').on('click', '.remove', function() {
                $('#removeModal').modal('toggle');

                var row = $(this).closest('tr');
                var  sid = row.find('input[name=sid]').val();
                var  uid = row.find('input[name=uid]').val();
                var name = row.find('.td-name').text();

                document.getElementById('stud_name').innerHTML = "Delete <strong>" + name + "</strong>'s Information?";
                $('#stud_id').attr({'value': sid});
                $('#uid').attr({'value': uid});
            });
        });

        /* Formatting function for row details - modify as you need */
        function students (d) {
            // `d` is the orginal data object for the row
            return '<div class="slider">' +
                    '<table cellspacing="0" border="0" width="650" id="student_details">'+
                        '<tbody class="child-color text-center">'+
                            '<tr>'+
                                '<td width="100">Gender:</td>'+
                                '<td>'+d.gender+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Birthday:</td>'+
                                '<td>'+d.birthday+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Address:</td>'+
                                '<td>'+d.address+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Year:</td>'+
                                '<td>'+d.year+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Program:</td>'+
                                '<td>'+d.program+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Actions:</td>'+
                                '<td>'+d.actions+'</td>'+
                            '</tr>'+
                        '</tbody>'+
                    '</table>'+
                '</div>';
        }
    </script>
</body>
</html>