<?php

class Instructor 
{
    private $_db,
            $_results,
            $_data,
            $_count = 0,
            $_first;

    public function __construct() 
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = array()) 
    {
        if (!$this->_db->insert('instructors', $fields)) 
        {
            throw new Exception("There was a problem in saving instructor's information, please try again later.");
        }
    }

    public function update($fields = array(), $id = null) 
    {
        if (!$this->_db->update('instructors', 'instructor_id', $id, $fields)) 
        {
            throw new Exception("There was a problem in updating instructor's information, please try again later.");
        }
    }

    public function updateGrades($fields = array(), $id = null) 
    {
        if (!$this->_db->update('grades', 'grade_id', $id, $fields)) 
        {
            throw new Exception('There was a problem in updating student&apos;s grades, please try again later.');
        }
    }

    public function addSubject($fields = array()) 
    {
        if (!$this->_db->insert('instructor_subjects', $fields)) 
        {
            throw new Exception("There was a problem in assigning subject to instructor, please try again later.");
        }
    }

    public function addAttendance($fields = array()) 
    {
        if (!$this->_db->insert('attendance', $fields)) 
        {
            throw new Exception("There was a problem in adding attendance to selected students, please try again later.");
        }
    }

    public function addQuiz($fields = array()) 
    {
        if (!$this->_db->insert('quizzes', $fields)) 
        {
            throw new Exception('There was a problem in adding quiz to student, please try again later.');
        }
    }

    public function addRecitation($fields = array()) 
    {
        if (!$this->_db->insert('recitations', $fields)) 
        {
            throw new Exception('There was a problem in adding recication to student, please try again later.');
        }
    }

    public function addExam($fields = array()) 
    {
        if (!$this->_db->insert('exams', $fields)) 
        {
            throw new Exception('There was a problem in adding exam to student, please try again later.');
        }
    }

    public function addGrade($fields = array()) 
    {
        if (!$this->_db->insert('grades', $fields)) 
        {
            throw new Exception('There was a problem in adding grade to student, please try again later.');
        }
    } 

    public function updateAttendance($student_id, $subject_id, $instructor_id, $desc, $day, $sem, $sy) 
    {
        if (!$this->_db->query("UPDATE attendance SET description = ? WHERE student_id = ? AND subject_id = ? AND instructor_id = ? AND day = ? AND semester = ? AND school_year = ?", array($desc, $student_id, $subject_id, $instructor_id, $day, $sem, $sy))) 
        {
            throw new Exception('There was a problem in updating attendance to selected students, please try again later.');
        }
    }

    public function getInstructors() 
    {
        $get = $this->_db->query("SELECT * FROM view_instructors");

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getInstructor($id = null) 
    {
        $get = $this->_db->get('view_instructors', array('instructor_id', '=', $id));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getInstructorByUserId($id = null) 
    {
        $get = $this->_db->get('view_instructors', array('user_id', '=', $id));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getTotalStudentsBySubject($subject_id, $section, $sy) 
    {
        $get = $this->_db->query("SELECT DISTINCT COUNT(student_id) AS total FROM view_total_students_by_subject 
                                  WHERE subject_id = ? 
                                  AND section_id = (SELECT section_id FROM sections WHERE section_name = ?) AND school_year = ?", array($subject_id, $section, $sy));

        if ($get->count()) 
        {
            $this->_count = $get->first()->total;
            return true;
        }

        return false;
    }

    public function getTotalSubjectsByInstructor($id, $sem, $sy) 
    {
        $count = $this->_db->query("SELECT COUNT(instructor_subject_id) AS totalSubjects FROM view_instructor_subjects_all 
                                    WHERE instructor_id = ? AND semester = ? AND school_year = ?", array($id, $sem, $sy));

        if ($count->count()) 
        {
            $this->_count = $count->first()->totalSubjects;
            return true;
        }

        return false;
    }

    public function getStudentsBySubject($id, $sy, $sem) 
    {
        $get = $this->_db->query("SELECT * FROM view_students_by_subjects WHERE subject_id = ? AND semester = ? AND school_year = ? ORDER BY lastname ASC", array($id, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getStudentAttendance($student_id, $subject_id, $instructor_id, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM view_student_attendance 
                                  WHERE student_id = ? 
                                    AND subject_id = ? 
                                    AND instructor_id = ? 
                                    AND semester = ? 
                                    AND school_year = ?", array($student_id, $subject_id, $instructor_id, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getInstructorSubjects($id, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM view_instructor_subjects WHERE instructor_id = ? AND semester = ? AND school_year = ?", array($id, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getStudentsName($subject_id, $instructor_id, $sem, $sy) 
    {
        $getNames = $this->_db->query("SELECT DISTINCT CONCAT(lastname,', ', firstname) AS fullName, student_id, student_no, year_id FROM view_students_by_subjects 
                                WHERE subject_id = ? 
                                AND instructor_id = ? 
                                AND semester = ? 
                                AND school_year = ?", array($subject_id, $instructor_id, $sem, $sy));

        if ($getNames->count()) 
        {
            $this->_results = $getNames->results();
            return true;
        }

        return false;
    }

    public function getQuizzes($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM quizzes 
                                  WHERE term = ?
                                    AND student_id = ? 
                                    AND subject_id = ? 
                                    AND instructor_id = ?
                                    AND year_id = ? 
                                    AND semester = ? 
                                    AND school_year = ?", array($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy));

        if ($get->count())
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getRecitations($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM recitations 
                                  WHERE term = ?
                                    AND student_id = ? 
                                    AND subject_id = ? 
                                    AND instructor_id = ?
                                    AND year_id = ? 
                                    AND semester = ? 
                                    AND school_year = ?", array($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getExams($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM exams 
                                  WHERE term = ?
                                    AND student_id = ? 
                                    AND subject_id = ? 
                                    AND instructor_id = ?
                                    AND year_id = ? 
                                    AND semester = ? 
                                    AND school_year = ?", array($category, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function getGrades($subject_id, $instructor_id, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM view_grades WHERE subject_id = ? AND instructor_id = ? AND semester = ? AND school_year = ?", array($subject_id, $instructor_id, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }

        return false;
    }

    public function removeSubjectFromInstructor($id) 
    {
        if (!$this->_db->delete('instructor_subjects', array('instructor_subject_id', '=', $id))) 
        {
            throw new Exception('There was a problem in removing subject from instructor, please try again later.');
        }
    }

    public function checkInstructorSubject($subject_id, $id, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM view_instructor_subjects_all 
                                    WHERE subject_id = ? 
                                        AND instructor_id = ?
                                        AND semester = ? 
                                        AND school_year = ?", array($subject_id, $id, $sem, $sy));

        if ($check->count()) 
        {
            return true;
        }

        return false;
    }

    public function checkGrade($term, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM view_grades 
                                    WHERE term = ? 
                                        AND student_id = ? 
                                        AND subject_id = ?
                                        AND instructor_id = ?
                                        AND year_id = ?
                                        AND semester = ? 
                                        AND school_year = ?", array($term, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy));

        if ($check->count()) 
        {
            return true;
        }

        return false;
    }

    public function checkExam($term, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM exams 
                                    WHERE term = ? 
                                        AND student_id = ? 
                                        AND subject_id = ?
                                        AND instructor_id = ?
                                        AND year_id = ?
                                        AND semester = ? 
                                        AND school_year = ?", array($term, $student_id, $subject_id, $instructor_id, $yr, $sem, $sy));

        if ($check->count()) 
        {
            return true;
        }

        return false;
    }

    public function checkAttendance($student_id, $subject_id, $instructor_id, $day, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM attendance 
                                    WHERE student_id = ?
                                    AND subject_id = ? 
                                    AND instructor_id = ?
                                    AND day = ? 
                                    AND semester = ?
                                    AND school_year = ?", array($student_id, $subject_id, $instructor_id, $day, $sem, $sy));

        if ($check->count()) 
        {
            $this->_first = $check->first();
            return true;
        }

        return false;
    }

    public function results() 
    {
        return $this->_results;
    }

    public function first() 
    {
        return $this->_first;
    }

    public function count() 
    {
        return $this->_count;
    }

    public function data() 
    {
        return $this->results()[0];
    }
}