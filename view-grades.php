<?php

require_once 'core/init.php';

$user = new User();
$student = new Student();

$location = basename($_SERVER['REQUEST_URI']);

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
                <h4>Academic Grades</h4>
                <hr>
                <div class="container">
                    <h4 class="text-center"><a role="button" id="expandTable" class="btn btn-green text-light expandTable" data-toggle="collapse" href="#collapseGrades" aria-expanded="false" aria-controls="collapseGrades">Show Yearly Academic Grade Record</a></h4>
                    <div class="collapse" id="collapseGrades"> 
                        <table class="table table-striped" id="view-grades">
                            <tbody class="text-center">
                                <?php
                                $student_year = array();

                                $year = DB::getInstance()->get('view_student_year', array('student_id', '=', $data->student_id));

                                if ($year->count()) 
                                {
                                    $count = 0;
                                    foreach ($year->results() as $row) 
                                    {
                                        $student_year[$count]['year_id'] = $row->year_id;
                                        $student_year[$count]['year'] = $row->year;
                                        $student_year[$count]['semester'] = $row->semester;
                                        $student_year[$count]['school_year'] = str_replace(' ', '', $row->school_year);
                                        $student_year[$count]['isEmpty'] = 'true';
                                        $count++;
                                    }

                                    for ($x = 0; $x < count($student_year); $x++) 
                                    {
                                        $yr = $student_year[$x]['year'] . ' ' . $student_year[$x]['semester'];
                                        $check = DB::getInstance()->query("SELECT * FROM view_grades WHERE student_id = ? AND CONCAT(year, ' ', semester) = ?", array($data->student_id, $yr));

                                        if ($check->count()) 
                                        {
                                            $student_year[$x]['isEmpty'] = 'false';
                                        }
                                    }
                                }
                                if (count($student_year) > 0) 
                                {
                                    for ($i = 0; $i < count($student_year); $i++) 
                                    {
                                        if ($student_year[$i]['isEmpty'] == 'false') 
                                        {
                                ?>
                                <tr>
                                        <td>
                                            <h6>
                                                <?php echo $student_year[$i]['year'] . " " . $student_year[$i]['semester'] . " ({$student_year[$i]['school_year']})" ?>&nbsp;
                                                <a role="button" class="btn btn-green text-light" href="?year=<?php echo $student_year[$i]['year_id'] ?>&semester=<?php echo $student_year[$i]['semester'] ?>&SY=<?php echo $student_year[$i]['school_year'] ?>">View</a>
                                            </h6>
                                        </td>
                                </tr>
                                <?php
                                        }
                                    }
                                } else { ?>
                                <tr>
                                    <td>
                                        <span class="text-center">No Record Found</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    if (Input::exists('get')) 
                    {
                        $yr = Input::get('year');
                        $sem = Input::get('semester');

                        $subjects = array();

                        $get = DB::getInstance()->query("SELECT * FROM view_get_students_subject_name WHERE student_id = ? AND year_id = ? AND semester = ?", array($data->student_id, $yr, $sem));

                        if ($get->count()) 
                        {
                            $count = 0;

                            foreach ($get->results() as $row) 
                            {
                                $subjects[$count]["code"] = $row->subject_code;
                                $subjects[$count]["section"] = $row->section_name;
                                $subjects[$count]["prelim"] = '--';
                                $subjects[$count]["midterm"] = '--';
                                $subjects[$count]["semi"] = '--';
                                $subjects[$count]["finals"] = '--';
                                $subjects[$count]['units'] = $row->units;
                                $subjects[$count]['gender'] = '';
                                $subjects[$count]['firstname'] = '';
                                $subjects[$count]['lastname'] = '';

                                $count++;
                            }

                            $getGrades = DB::getInstance()->query("SELECT * FROM view_grades WHERE student_id = ? AND year_id = ? AND semester = ?", array($data->student_id, $yr, $sem));

                            if ($getGrades->count()) 
                            {
                                for($i = 0; $i < count($subjects); $i++) 
                                {
                                    foreach ($getGrades->results() as $row) 
                                    {
                                        if ($subjects[$i]['code'] == $row->subject_code) 
                                        {

                                            if ('Prelim' == $row->term) 
                                            {
                                                $subjects[$i]['prelim'] = $row->grade;
                                            } 
                                            else if ('Midterm' == $row->term) 
                                            {
                                                $subjects[$i]['midterm'] = $row->grade;
                                            } 
                                            else if ('Semi' == $row->term) 
                                            {
                                                $subjects[$i]['semi'] = $row->grade;
                                            } 
                                            else if ('Finals' == $row->term) 
                                            {
                                                $subjects[$i]['finals'] = $row->grade;
                                            }

                                            $subjects[$i]['gender'] = $row->gender;
                                            $subjects[$i]['lastname'] = $row->lastname;
                                            $subjects[$i]['firstname'] = $row->firstname;
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                    <hr>
                    <h5 class="text-center"><?php echo getYearSem();  ?></h5>
                    <hr>
                    <table border="0" width="650" class="custom-table" id="record">
                        <tbody>
                            <tr>
                                <th>SUBJECTS</th>
                                <th>PRE</th>
                                <th>MID</th>
                                <th>SEMI</th>
                                <th>FINAL</th>
                                <th>AVE</th>
                            </tr>
                            <?php
                                for ($i = 0; $i < count($subjects); $i++) 
                                {
                                    $prelim = formatGrade($subjects[$i]['prelim']);
                                    $midterm = formatGrade($subjects[$i]['midterm']);
                                    $semi = formatGrade($subjects[$i]['semi']);
                                    $finals = formatGrade($subjects[$i]['finals']);
                                    
                                    if ($prelim != '--' && $midterm != '--' && $semi != '--' && $finals != '--') 
                                    {
                                        $avg = ($prelim + $midterm + $semi + $finals) / 4;
                                        $avg = number_format((float)$avg, 2, '.', '');

                                        $result = getAverage($avg);
                                    } 
                                    else 
                                    {
                                        $avg = '--';
                                        $result = '--';
                                    }

                                    
                                    if ($i % 2 == 0) 
                                    {
                                        echo '<tr class="row-gray bottom_border">';
                                    } 
                                    else 
                                    {
                                        echo '<tr class="bottom_border">';
                                    }

                                    echo '<td class="c-green">' . $subjects[$i]['code'] . '</td>';
                                    echo '<td class="c-green">' . $prelim . '</td>';
                                    echo '<td class="c-green">' . $midterm . '</td>';
                                    echo '<td class="c-green">' . $semi . '</td>';
                                    echo '<td class="c-green">' . $finals . '</td>';
                                    echo '<td class="c-green">' . $result . '</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php
                        echo '<h6 class="text-center row-gray">***NOTHING FOLLOWS***</h6>';
                    }
                    ?>
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
        $(document).ready(function(){
            $('.expandTable').click(function(){
                if ($('.expandTable').attr('aria-expanded') === 'false') {
                    $('.expandTable').attr('aria-expanded', true);
                    document.getElementById('expandTable').innerHTML = 'Hide Yearly Academic Grade Record';
                } else {
                    $('.expandTable').attr('aria-expanded', false);
                    document.getElementById('expandTable').innerHTML = 'Show Yearly Academic Grade Record';
                }
            });
        });
    </script>
</body>
</html>
<?php
function getYearSem() {
    if (Input::exists('get')) 
    {
        if (Input::get('year') == 1) 
        {
            return Input::get('year') . 'st Year ' . Input::get('semester') . ' (' . Input::get('SY') . ')';
        } 
        else if (Input::get('year') == 2) 
        {
            return Input::get('year') . 'nd Year ' . Input::get('semester') . ' (' . Input::get('SY') . ')';
        } 
        else if (Input::get('year') == 3) 
        {
            return Input::get('year') . 'rd Year ' . Input::get('semester') . ' (' . Input::get('SY') . ')';
        } 
        else if (Input::get('year') == 4)
        {
            return Input::get('year') . 'th Year ' . Input::get('semester') . ' (' . Input::get('SY') . ')';
        }
        return;
    }
}

function formatGrade($grade) {
    if (ctype_digit($grade)) 
    {
        if ($grade >= 99 || $grade == 100) 
        {
            return '1.00';
        } 
        else if ($grade >= 96 || $grade == 98) 
        {
            return '1.25';
        } 
        else if ($grade >= 93 || $grade == 95) 
        {
            return '1.50';
        } 
        else if ($grade >= 90 || $grade == 92) 
        {
            return '1.75';
        } 
        else if ($grade >= 87 || $grade == 89) 
        {
            return '2.00';
        } 
        else if ($grade >= 84 || $grade == 86) 
        {
            return '2.25';
        } 
        else if ($grade >= 81 || $grade == 83) 
        {
            return '2.50';
        } 
        else if ($grade >= 78 || $grade == 80) 
        {
            return '2.75';
        } 
        else if ($grade >= 75 || $grade == 77) 
        {
            return '3.00';
        } 
        else 
        {
            return '5.00';
        }
    } 
    else 
    {
        return $grade;
    }
}

function getAverage($avg) {
    $whole = floor($avg);
    $fraction = $avg - $whole;
    $fraction = str_replace('.', '', $fraction);

    if ($fraction < 9) 
    {
        $result = $whole . '.00';
        return $result;
    } 
    else if ($fraction > 10 && $fraction < 30) 
    {
        $result = $whole . '.25';
        return $result;
    } 
    else if ($fraction > 31 && $fraction < 60) 
    {
        $result = $whole . '.50';
        return $result;
    } 
    else if ($fraction > 61 && $fraction < 80) 
    {
        $result = $whole . '.75';
        return $result;
    } 
    else 
    {
        if ($whole == 3) 
        {
            if (($fraction < 9) || ($fraction > 10 && $fraction < 30) || ($fraction > 31 && $fraction < 60) || ($fraction > 61 && $fraction < 80)) 
            {
                $result = $whole . '.00';
                return $result;
            }
        } 
        else if ($whole == 4 || $whole == 5) 
        {
            $result = $whole . '.00';
            return $result;
        }
    }
}