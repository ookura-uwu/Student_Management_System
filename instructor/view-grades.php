<?php

require_once '../core/init.php';
require_once 'school-year.php';

$user = new User();
$student = new Student();
$instructor = new Instructor();

$location = basename($_SERVER['REQUEST_URI']);

if (($user->isLoggedIn() && !$user->hasPermission('Instructor')) || (!$user->isLoggedIn())) 
{
    Redirect::to('../');
}

$instructor->getInstructorByUserId($user->data()->user_id);
$data = $instructor->data();

if (Input::exists()) 
{
    if (Input::get('btn-grade')) 
    {
        try 
        {
            $instructor->addGrade(array(
                'term' => Input::get('term'),
                'grade' => Input::get('grade'),
                'grade_date' => Input::get('grade_date'),
                'year_id' => Input::get('year__id'),
                'subject_id' => Input::get('subject'),
                'student_id' => Input::get('student__id'),
                'instructor_id' => $data->instructor_id,
                'semester' => $semester,
                'school_year' => $school_year
            ));

            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Grade</strong> has been added to student!</div>');
            Redirect::to($location);
        } 
        catch (Exception $e) 
        {
            Session::flash('result', '<div class="alert alert-success mb-2"><strong>Grade</strong> has been added to student!</div>');
            Redirect::to($location);
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
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a></li>
                        <li class="breadcrumb-item"><a href="my-subjects.php"   ><i class="fas fa-table"></i> &nbsp;My Subjects</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-users"></i> &nbsp;View Students</li>
                    </ol>
                </nav>
                <h4>View Grades</h4>
                <hr>
                <div class="form-row">
                    <form action="" method="get">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Subject:</span>
                                    </div>
                                    <select class="custom-select" id="subject" width="100%" name="subject" onchange="this.form.submit();">
                                        <option value="default">Select Subject</option>
                                        <?php
                                        if ($instructor->getInstructorSubjects($data->instructor_id, $semester, $school_year)) 
                                        {
                                            foreach ($instructor->results() as $row) 
                                            {
                                        ?>
                                        <option value="<?php echo $row->subject_id ?>" <?php (Input::exists('get') ? (Input::get('subject') == $row->subject_id ? print 'selected' : '') : '') ?>><?php echo $row->subject_code ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <h3 class="text-center">
                    <?php 
                    if (Input::exists('get')) {
                        $get = DB::getInstance()->query("SELECT * FROM view_instructor_subjects 
                                                         WHERE instructor_id = ? 
                                                            AND subject_id = ? 
                                                            AND semester = ? 
                                                            AND school_year = ?", array($data->instructor_id, Input::get('subject'), $semester, $school_year))->first();
                    
                        echo $get->subject_code . ': ' . $get->subject_name;
                    }
                    ?>        
                </h3>
                <hr>
                <?php echo (Session::exists('result') ? Session::flash('result') : '') ?>
                <br>
                    <table border="0" id="grades_list">
                        <thead class="text-center">
                            <th>Name</th>
                            <th>Action</th>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $names = array();

                            if (Input::exists('get') && Input::get('subject') != 'default') 
                            {
                                if ($instructor->getStudentsName(Input::get('subject'), $data->instructor_id, $semester, $school_year)) 
                                {
                                    $count = 0;
                                    foreach ($instructor->results() as $name) 
                                    {
                                        $names[$count]['name'] = $name->fullName;
                                        $names[$count]['id'] = $name->student_id;
                                        $names[$count]['student_no'] = $name->student_no;
                                        $names[$count]['prelim'] = '';
                                        $names[$count]['midterm'] = '';
                                        $names[$count]['semi'] = '';
                                        $names[$count]['finals'] = '';
                                        $names[$count]['year_id'] = $name->year_id;
                                        $count++;
                                    }

                                    if ($instructor->getGrades(Input::get('subject'), $data->instructor_id, $semester, $school_year)) 
                                    {
                                        for ($i = 0; $i < count($names); $i++) 
                                        {
                                            foreach ($instructor->results() as $row) 
                                            {
                                                if ($names[$i]['name'] == $row->fullName) 
                                                {
                                                    if ('Prelim' == $row->term) 
                                                    {
                                                        $names[$i]['prelim'] = $row->grade;
                                                    } 
                                                    else if ('Midterm' == $row->term) 
                                                    {
                                                        $names[$i]['midterm'] = $row->grade;
                                                    } 
                                                    else if ('Semi' == $row->term) 
                                                    {
                                                        $names[$i]['semi'] = $row->grade;
                                                    } 
                                                    else if ('Finals' == $row->term) 
                                                    {
                                                        $names[$i]['finals'] = $row->grade;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (count($names) > 0) 
                                    {
                                        sort($names);
                                        foreach ($names as $name) 
                                        {
                                            $code = $name['student_no'];
                                            $subj = Input::get('subject');
                                            $ln = substr($name['name'], 0, 1);
                                            $code = $ln . $code;

                                            if ($name['prelim'] == '' && $name['midterm'] == '' && $name['semi'] == '' && $name['finals'] == '') 
                                            {
                                                $button = "<a role='button' id='edit-grade' href='edit-grades.php?student={$name['id']}&subject={$subj}' class='btn btn-success btn-sm disabled mb-1'>
                                                        <i class='fas fa-edit'></i>";
                                            } 
                                            else 
                                            {
                                                $button = "<a role='button' id='edit-grade' href='edit-grades.php?student={$name['id']}&subject={$subj}' class='btn btn-success btn-sm mb-1'>
                                                        <i class='fas fa-edit'></i>";
                                            }

                                            if ($count % 2 == 0) 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border row-gray'>";
                                            } 
                                            else 
                                            {
                                                echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                            }

                                            echo "<td class='head'>
                                                    <input type='hidden' name='student_id' value='{$name['id']}'>
                                                    <input type='hidden' name='year_id' value='{$name['year_id']}'>
                                                    {$name['name']}
                                                 </td>";
                                            echo "<td class='head'>
                                                    {$button}
                                                    </a>
                                                  </td>";
                                            echo '</tr>';


                                            // Child row data
                                            echo '<tr class="child-color">';
                                            echo '<td>';
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        Prelim:
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        Midterm:
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        SemiFinal:
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        Finals:
                                                  </div>";
                                            echo '</td>';

                                            echo '<td>';
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        {$name['prelim']}
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        {$name['midterm']}
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        {$name['semi']}
                                                  </div>";
                                            echo "<div class='accordian-body collapse {$code}'>
                                                        {$name['finals']}
                                                  </div>";
                                            echo '</td>';
                                            echo '</tr>';

                                            $count++;
                                        }
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <div class="modal fade" id="gradeModal" tabindex="-1" role="document" aria-hidden="true">
                        <div class="modal-dialog" role="dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>Add Grade</h5>
                                    <button type="button" class="close" data-dismiss="modal" id="close-gradeModal" aria-label="Close"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="post">
                                        <!-- Grade -->
                                        <div id="result"></div>
                                        <div id="grade">
                                            <form action="" method="post">
                                                <input type="hidden" name="student__id" class="__student__id" id="__student__id">
                                                <input type="hidden" name="year__id" class="__year__id">
                                                <input type="hidden" name="category" value="Grade">
                                                <h6>Grade</h6>
                                                <div class="form-row mb-2">
                                                    <div class="col-xl-6 mx-auto mb-1">
                                                        <select class="custom-select" name="term" id="term" onchange='checkGrade(document.getElementById("term").value, document.getElementById("__student__id").value, <?php echo json_encode(Input::get('subject')) ?>, <?php echo json_encode($data->instructor_id) ?>)' required>
                                                            <option value="">Select Term</option>
                                                            <option value="Prelim">Prelim</option>
                                                            <option value="Midterm">Midterm</option>
                                                            <option value="Semi">SemiFinal</option>
                                                            <option value="Finals">Finals</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-6 mx-auto mb-1">
                                                        <input type="text" class="form-control" name="grade" placeholder="Grade">
                                                    </div>
                                                </div>
                                                <div class="form-row mb-2">
                                                    <div class="col-xl-12">
                                                        <label for="grade_date">Exam Date</label>
                                                        <input type="text" id="grade_date" name="grade_date" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-row mb-2">
                                                    <div class="col-xl-12">
                                                        <button type="submit" class="btn btn-primary btn-block" id="btn-grade" name="btn-grade" value="Grade">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- Grade -->
                                    </form>
                                </div>
                            </div>
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
            // $('#grades_list').DataTable({
            //     'order': [[0, 'asc']]
            // });

            $('#grade_date').datepicker();

            $('[data-toggle="tooltip"]').tooltip();

            $('.add-grade').click(function(){
                $('#gradeModal').modal('toggle');

                var row = $(this).closest('tr');
                var stud_id = row.find('input[name="student_id"]').val();
                var year_id = row.find('input[name="year_id"]').val();

                $('.__student__id').attr({'value': stud_id});
                $('.__year__id').attr({'value': year_id});
            });

            $('#close-gradeModal').click(function() {
                document.getElementById('result').innerHTML = "";
                document.getElementById('btn-grade').disabled = false;
                document.getElementById('term').value = '';
            });

        });

        function checkGrade(term, id, sid, iid) {
            if (term == "") 
            {
                document.getElementById('result').innerHTML = "";
                return;
            }

            if (window.XMLHttpRequest) 
            {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } 
            else 
            { // IE6 IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) 
                {
                    document.getElementById('result').innerHTML = this.responseText;

                    var result = this.responseText;
                    if (result == "") 
                    {
                        document.getElementById('btn-grade').disabled = false;
                    } 
                    else 
                    {
                        document.getElementById('btn-grade').disabled = true;
                    }

                }
            }

            xmlhttp.open("GET", "checkGrade.php?term=" + term + "&student=" + id + "&subject=" + sid + "&instructor=" + iid);
            xmlhttp.send();
        }
    </script>
</body>
</html>