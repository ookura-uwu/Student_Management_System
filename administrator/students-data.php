<?php

require_once '../core/init.php';

$student = new Student();

if ($student->getList()) 
{
    foreach ($student->results() as $row) 
    {
        $mi = ($row->middlename == '' ? '' : substr($row->middlename, 0, 1) . '.');
        $name = '<strong>' . strtoupper($row->lastname) . '</strong>, ' . $row->firstname . ' ' . $mi;

        $program = '';
        if ($row->program == 'Bachelor of Science in Computer Science')
            $program = 'BSCS';
        else if ($row->program == 'Bachelor of Technical Teacher Education')
            $program = 'BTTE';
        else if ($row->program == 'Bachelor of Technical Vocational Teacher Education')
            $program = 'BTVTE';

        $item[] = array(
            'student_no' => "<input type='hidden' name='sid' value='{$row->student_id}'><input type='hidden' name='uid' value='{$row->user_id}'>" . $row->student_no,
            'name' => $name,
            'gender' => $row->gender,
            'birthday' => $row->birthday,
            'address' => $row->address,
            'year' => $row->year,
            'program' => $program,
            'delete' => '<a role="button" class="btn btn-danger btn-sm mb-1 text-light remove mini" data-toggle="tooltip" title="Delete Student Information" data-placement="top" style="background-color: #F80000"><i class="fas fa-times"></i></a>',

            'actions' => '<a href="student.php?action=edit&id=' . $row->student_id . '" role="button" class="btn btn-primary btn-sm mb-1 mini" data-toggle="tooltip" data-placement="top" title="Edit Information"><i class="far fa-edit"></i></a>
                        <a href="view-subjects-list.php?student=' . $row->student_id . '&year=' . $row->year_id . '&type=student" role="button" class="btn btn-warning btn-sm mb-1 text-light mini" data-toggle="tooltip" data-placement="top" title="View Current Subjects"><i class="fas fa-table"></i></a>'
        );
    }

}

$output = array('data' => (!empty($item) ? $item : ''));
echo json_encode($output);
?>

                                        
                                        