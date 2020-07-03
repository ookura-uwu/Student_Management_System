<?php

require_once '../core/init.php';
require_once 'school-year.php';

$instructor = new Instructor();

if (Input::exists('get')) 
{
    $term = Input::get('term');
    $id = Input::get('student');
    $sid = Input::get('subject');
    $iid = Input::get('instructor');
    $yr = Input::get('year');

    if ($instructor->checkExam($term, $id, $sid, $iid, $yr, $semester, $school_year)) 
    {
        echo '<div class="alert alert-warning">Selected Term already exists.</div>';
    } 
    else 
    {
        echo '';
    }

}