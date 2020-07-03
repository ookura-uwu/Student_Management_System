<?php

require_once 'core/init.php';

$student = new Student();

if ($student->getCurrentSchoolYear()) 
{
    $school_year = $student->first()->schoolyear;
    $semester = $student->first()->semester;
    $sy_id = $student->first()->sy_id;
}