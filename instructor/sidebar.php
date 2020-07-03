<?php

require_once '../core/init.php';

$user = new User();
$instructor = new Instructor();

$location = basename($_SERVER['REQUEST_URI']);

if (($user->isLoggedIn() && !$user->hasPermission('Instructor')) || (!$user->isLoggedIn())) 
{
    Redirect::to(404);
}

$instructor->getInstructorByUserId($user->data()->user_id);
$data = $instructor->data();

?>
<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="ml-2 text-center the-title">Student Management System</h3>
        <br>
    </div>

    <ul class="list-unstyled components">
        <span class="text-center">
            <p>WELCOME,</p>
            <h5><?php echo strtoupper($data->lastname) . ', ' . $data->firstname ?></h5>
            <h6>Instructor</h6>
            <br>
        </span>
        <br>
        <li>
            <a href="./"><i class="ml-2 fas fa-tachometer-alt"></i> &nbsp;Home</a>
        </li>
        <li>
            <a href="attendance.php"><i class="ml-2 fas fa-tasks"></i> &nbsp;Attendance</a>
        </li>
        <li>
            <a href="my-subjects.php"><i class="ml-2 fas fa-table"></i> &nbsp;My Subjects</a>
        </li>
        <li>
            <a class="dropdown-toggle" href="#grades" data-toggle="collapse" aria-expanded="false"><i class="ml-2 fas fa-book-open"></i> &nbsp;Grades</a>
            <ul class="collapse list-unstyled" id="grades">
                <li>
                    <a href="view-grades.php"><i class="fas fa-minus mr-2"></i>&nbsp;View Grades</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>