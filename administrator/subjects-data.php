<?php

require_once '../core/init.php';
require_once 'school-year.php';

$student = new Student();

if ($student->getSubjects($semester, $school_year)) 
{
    foreach ($student->results() as $row) 
    {
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

        $time = $row->class_starts . ' - ' . $row->class_ends;

        $item[] = array(
            'code' => $row->subject_code,
            'name' => '<strong>' . $row->subject_name . '</strong>',
            'units' => $row->units,
            'section' => $row->section_name,
            'days' => $days,
            'time' => $time,
            'semester' => $row->semester,
            'sy' => $row->school_year
        );
    }

}

$output = array('data' => (!empty($item) ? $item : ''));
echo json_encode($output);
?>

                                        
                                        