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
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Subjects</li>
                    </ol>
                </nav>
                <h4>Subjects</h4>
                <hr>

                <a class="btn btn-primary" href="add-subject.php"><i class="fas fa-plus-square"></i> &nbsp;Add Subject</a>
                <a role="button" class="btn btn-primary" href="assign-subjects.php">Add Subjects to Students</a>
                <hr><br>

                <h6>Subjects List</h6>
                <hr>
                <div class="container">
                    <table border="0" cellspacing="0" id="subjects_list" width="100%">
                        <thead class="text-center">
                            <th width="100">Subject Code</th>
                            <th>Subject Name</th>
                        </thead>
                        <tbody class="tdata text-center"></tbody>
                    </table>
                </div>
            </div><br>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php'; ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#subjects_list').DataTable({
                "autoWidth": true,
                "ajax": {
                    url: "subjects-data.php",
                    type: "post"
                },
                "columns": [
                    { 
                        "className": "details-control",
                        "data": "code"
                    },
                    {
                        "className": "details-control",
                        "data": "name"
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
            $('#subjects_list tbody').on('click', 'td.details-control', function() {
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
                    row.child(subjects(row.data()), 'no-padding').show();
                    tr.addClass('shown');

                    $('div.slider', row.child()).slideDown();
                }
            });

            $('[data-toggle="tooltip"]').tooltip();

        });

        /* Formatting function for row details - modify as you need */
        function subjects (d) {
            // `d` is the orginal data object for the row
            return '<div class="slider">' +
                    '<table cellspacing="0" border="0" width="650" id="subjects_details">'+
                        '<tbody class="child-color text-center">'+
                            '<tr>'+
                                '<td width="100">Section:</td>'+
                                '<td>'+d.section+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Units:</td>'+
                                '<td>'+d.units+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Schedule:</td>'+
                                '<td>'+d.days+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Time:</td>'+
                                '<td>'+d.time+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">Semester:</td>'+
                                '<td>'+d.semester+'</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td width="100">School Year:</td>'+
                                '<td>'+d.sy+'</td>'+
                            '</tr>'+
                        '</tbody>'+
                    '</table>'+
                '</div>';
        }
    </script>
</body>
</html>