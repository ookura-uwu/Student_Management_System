<?php

require_once '../core/init.php';

$student = new Student();

if (Input::exists('get')) 
{
    $sy = Input::get('sy');
    $sem = Input::get('sem');

    if ($student->checkSchoolYear($sy, $sem)) 
    {
        echo true;
    } 
    else 
    {
        echo false;
    }

}