<?php

require_once '../core/init.php';

$student = new Student();

if ($student->getCurrentSchoolYear()) 
{
    $school_year = $student->first()->schoolyear;
    $semester = $student->first()->semester;
    $sy_id = $student->first()->sy_id;
} 
else 
{
    $sy_message = "<div class='alert alert-danger'>School Year is not set!
                        <a class='btn btn-danger text-light mb-1' data-toggle='modal' data-target='#editSYModal'>Set School Year</a>
                    </div>";
}