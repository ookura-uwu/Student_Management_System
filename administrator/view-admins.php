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
    if (!$student->checkSchoolYear(Input::get('school_year'), Input::get('semester'))) {
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


if (Input::exists() && Input::get('confirm')) 
{
    try 
    {
        $deleteAdmin = DB::getInstance()->delete('users', array('user_id', '=', Input::get('user_id')));

        Session::flash('result', '<div class="alert alert-success">Administrator has been removed successfully!</div><br>');
        Redirect::to($location);
    } 
    catch (Exception $e) 
    {
        Session::flash('result', '<div class="alert alert-danger">' . $e->getMessage() . '</div>');
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
                        <li class="breadcrumb-item"><i class="fas fa-users"></i> &nbsp;Users Management</li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;Administrators List</li>
                    </ol>
                </nav>

                <?php 
                // Display result of SchoolYear/Semester Settings
                echo (Session::exists('result') ? Session::flash('result') : ''); 
                ?>

                <h4>Administrators List</h4>
                <hr>
                <a role="button" href="add-admin.php"  class="btn btn-primary"><i class="fas fa-address-card"></i> &nbsp;Add Administrator</a>

                <div class="jumbotron" style="background: #fff !important">
                    <div class="table-responsive">
                        <table border="0" id="admins_list">
                            <thead>
                                <th>Admin Name</th>
                                <th class="text-center">Actions</th>
                            </thead>
                            <tbody>
                                <?php
                                $get = DB::getInstance()->query('SELECT * FROM users WHERE group_id = 3 ORDER BY lastname ASC');
                                if ($get->count()) {
                                    $count = 0;
                                    foreach ($get->results() as $row) 
                                    {
                                        if ($count % 2 == 0) 
                                        {
                                            echo '<tr class="row-gray bottom_border">';
                                        } 
                                        else 
                                        {
                                            echo '<tr class="bottom_border">';
                                        }
                                ?>
                                    <td class="c-green"><input type="hidden" name="uid" value="<?php echo $row->user_id ?>"><?php echo strtoupper($row->lastname) . ', ' . $row->firstname . ' ' . $row->middlename; ?></td>
                                    <td class="c-green"><a role="button" class="btn btn-sm text-light remove" id="remove" style="background-color: #F80000"><i class="fas fa-times"></i></a></td>
                                </tr>
                                <?php
                                        $count++;   
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="modal fade" id="removeModal" role="document">
                            <div class="modal-dialog" role="dialog">
                                <div class="modal-content">
                                    <form action="" method="post">
                                        <div class="modal-header modal-header-crimson">
                                            <h4 class="text-light">Remove Administrator?</h4>
                                            <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span class="text-light">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            Confirm deletion of this administrator?
                                            <input type="hidden" name="user_id" id="user_id">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger" name="confirm" value="true">Remove</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
            $('#admins_list').DataTable({
                "order": [[1, "asc"]],
                "lengthMenu": [[10, 25, 50], [10, 25, 50]],
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bAutoWidth": false
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('.remove').click(function() {
                $('#removeModal').modal('toggle');

                var row = $(this).closest('tr');
                var uid = row.find('input[name=uid]').val();

                $('#user_id').attr({'value':uid});
            });
        });
        
    </script>
</body>
</html>