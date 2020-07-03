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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-table"></i> &nbsp;My Subjects</li>
                    </ol>
                </nav>
                <h4>My Subjects</h4>
                <hr><br>
                <div class="">
                    <table class="table-condensed" border="0" id="my-subjects_list" style="border-collapse: collapse;">
                        <thead>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                        </thead>
                        <tbody class="c-green text-center">
                            <?php
                            if (isset($semester) && (isset($school_year)))
                            {
                                if ($instructor->getInstructorSubjects($data->instructor_id, $semester, $school_year)) 
                                {
                                    $count = 0;
                                    foreach($instructor->results() as $row) 
                                    {
                                        $code = str_replace(' ', '', $row->subject_code);

                                        $result = '';
                                        if ($instructor->getTotalStudentsBySubject($row->subject_id, $row->section_name, $school_year)) 
                                        {
                                            $result = $instructor->count();
                                        }

                                        if ($count % 2 == 0) 
                                        {
                                            echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border row-gray'>";
                                        } 
                                        else 
                                        {
                                            echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                        }

                                        echo "<td class='head'>{$code}</td>";
                                        echo "<td class='head'>{$row->subject_name}</td>";
                                        echo '</tr>';

                                        // Child row data
                                        echo '<tr class="child-color">';
                                        echo '<td>';
                                        echo "<div class='accordian-body collapse {$code} text-right'>
                                                    Schedule:
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-right'>
                                                    Section:
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-right'>
                                                    Units:
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-right'>
                                                    No. of Students:
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-right'>
                                                        Actions:
                                            </div>";
                                        echo '</td>';

                                        echo '<td>';
                                        echo "<div class='accordian-body collapse {$code} text-center'>
                                                    {$row->sched}
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-center'>
                                                    {$row->section_name}
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-center'>
                                                    {$row->units}
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-center'>
                                                    {$result}
                                              </div>";
                                        echo "<div class='accordian-body collapse {$code} text-center'>
                                                    <a class='btn btn-link btn-sm' href='view-students.php?subject={$row->subject_id}'>View Students</a>
                                            </div>";
                                        echo '</td>';
                                        echo '</tr>';
                                        
                                        $count++;
                                    }
                                }
                            }
                            
                            ?>
                        </tbody>
                    </table>
                    <br><br>
                </div>
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