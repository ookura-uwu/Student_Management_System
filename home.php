<?php

require_once 'core/init.php'; 
require_once 'school-year.php';

$user = new User();
$student = new Student();

$semester = (isset($semester) ? $semester : '');
$school_year = (isset($school_year) ? $school_year : '');

if ((!$user->isLoggedIn()) || ($user->isLoggedIn() && !$user->hasPermission('Standard user'))) 
{
    Redirect::to('./');
}

$student->getStudentByUserId($user->data()->user_id);
$data = $student->data();

?>
<?php include 'header.php'; ?>
<body>
    <div id="progress" class="waiting">
        <dt></dt>
        <dd></dd>
    </div>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Page content -->
        <div id="content">
            <?php include 'navbar.php'; ?>

            <div class="container-fluid" id="_content" style="display: none;">
                <div class="swipe-area"></div>
                <h4 style="color: #21561e">Student Profile</h4>
                <hr>
                <table border="0" width="650" id="profile">
                    <tbody class="c-green">
                        <tr class="row-gray">
                            <td>Student Number:</td>
                            <td><?php echo $data->student_no ?></td>
                        </tr>
                        <tr>
                            <td>Full Name:</td>
                            <td><?php echo strtoupper($data->lastname) . ', ' . strtoupper($data->firstname) ?></td>
                        </tr>
                        <tr class="row-gray">
                            <td>Birthday:</td>
                            <td><?php echo date('F j, Y', strtotime($data->birthday)) ?></td>
                        </tr>
                        <tr>
                            <td>Program:</td>
                            <td><?php echo $data->program ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h4 style="color: #21561e">Schedule</h4>
                <hr>
                <div class="">
                    <table border="0" width="650" id="sched" style="border-collapse: collapse;">
                        <thead>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                        </thead>
                        <tbody class="c-green">

                            <?php
                            $get = DB::getInstance()->query("SELECT * FROM view_student_schedule WHERE student_id = ? AND year_id = ? AND semester = ? AND school_year = ?", array($data->student_id, $data->year_id, $semester, $school_year));
                            if ($get->count()) 
                            {
                                $count = 0;
                                foreach ($get->results() as $row) 
                                {
                                    $code = str_replace(' ', '', $row->subject_code);

                                    if ($row->class_days == 'Mon/Tue/Wed/Thu/Fri') 
                                    {
                                        $days = 'Mon - Fri';
                                    } 
                                    else if ($row->class_days == 'Mon/Tue/Wed/Thu/Fri/Sat') 
                                    {
                                        $days = 'Mon - Sat';
                                    } 
                                    else 
                                    {
                                        $days = $row->class_days;
                                    }

                                    if ($count % 2 == 0) 
                                    {
                                        echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border row-gray'>";
                                    } 
                                    else 
                                    {
                                        echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                    }

                                    echo "<td>{$code}</td>";
                                    echo "<td>{$row->subject_name}</td>";
                                    echo '</tr>';

                                    // Child row data
                                    echo '<tr class="child-color text-center">';
                                    echo '<td class="text-center">';
                                    echo "<div class='accordian-body collapse {$code} child-color nopadding'><span class='ml-4'>Class Days:</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>Class Time:</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>Section:</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>Units:</span></div>";
                                    echo '</td>';
                                    
                                    echo '<td class="text-center">';
                                    echo "<div class='accordian-body collapse {$code} child-color nopadding'><span class='ml-4'>{$row->class_days}</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>{$row->clsTime}</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>{$row->section_name}</span></div>";
                                    echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>{$row->units}</span></div>";
                                    echo '</td>';
                                    echo '</tr>';

                                    $count++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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
</body>
</html>