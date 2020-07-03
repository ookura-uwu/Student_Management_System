<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

if (($user->isLoggedIn() && !$user->hasPermission('Instructor')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

$instructor->getInstructorByUserId($user->data()->user_id);
$data = $instructor->data();

$location = basename($_SERVER['REQUEST_URI']);

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
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><a href="my-subjects.php"><i class="fas fa-table"></i> &nbsp;My Subjects</a></li>
                        <li class="breadcrumb-item"><a href="view-students.php<?php (Input::exists('get') ? print '?subject=' . Input::get('subject') : '')  ?>"><i class="fas fa-users"></i> &nbsp;View Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-tasks"></i>Attendance</li>
                    </ol>
                </nav>
                <h4>
                    <?php
                    if (Input::exists('get')) 
                    {
                        $get = DB::getInstance()->get('students', array('student_id', '=', Input::get('student')))->first();
                        $subj = DB::getInstance()->get('subjects', array('subject_id', '=', Input::get('subject')))->first();

                        echo '<h5>' . $get->firstname . ' ' . $get->lastname . '&apos;s Attendance </h5><hr><br>';
                        echo '<h5 class="text-center">' . $subj->subject_code . ': ' . $subj->subject_name . '</h5>';
                    }
                    ?>
                </h4>
                <hr>
                <div class="container">
                    <div class="table-responsive">
                        <table border='0' id="attendance_list">
                            <thead class="text-center">
                                <th>Date</th>
                                <th>Description</th>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                if (Input::exists('get') && Input::get('subject') != 'default') 
                                {
                                    if ($instructor->getStudentAttendance(Input::get('student'), Input::get('subject'), $data->instructor_id, $semester, $school_year)) 
                                    {
                                        $count = 0;
                                        foreach ($instructor->results() as $row) 
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
                                    <td class="c-green"><?php echo $row->day ?></td>
                                    <td class="c-green"><?php echo $row->description ?></td>
                                </tr>
                                <?php
                                            $count++;
                                        }
                                    }
                                }
                                ?>
                            </tbody>
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
            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>
</body>
</html>