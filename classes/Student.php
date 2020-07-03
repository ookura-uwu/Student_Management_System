<?php

class Student 
{
    private $_db,
            $_results,
            $_data,
            $_count = 0;

    public function __construct() 
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = array()) 
    {
        if (!$this->_db->insert('students', $fields)) 
        {
            throw new Exception('There was a problem in adding information, please try again later.');
        }
    }

    public function update($fields = array(), $id = null) 
    {
        if (!$this->_db->update('students', 'student_id', $id, $fields)) {
            throw new Exception('There was a problem in updating information, please try again later.');
        }
    }

    public function addSection($fields = array()) 
    {
        if (!$this->_db->insert('sections', $fields)) 
        {
            throw new Exception('There was a problem in adding new section, please try again later.');
        }
    }

    public function addSubject($fields = array()) 
    {
        if (!$this->_db->insert('subjects', $fields)) 
        {
            throw new Exception('There was a problem in adding new subject, please try again later.');
        }
    }

    public function addToSection($fields = array()) 
    {
        if (!$this->_db->insert('student_section', $fields)) 
        {
            throw new Exception('There was a problem adding students to selected section, please try again later.');
        }
    }

    public function addToStudent($fields = array()) 
    {
        if (!$this->_db->insert('student_subject', $fields)) 
        {
            throw new Exception('There was a problem adding subject to selected student, please try again later.');
        }
    }

    public function addStudentYear($fields = array()) 
    {
        if (!$this->_db->insert('student_year', $fields)) 
        {
            throw new Exception('There was a problem in saving student&apos;s year level, please try again later.');
        }
    }

    public function editSchoolYear($fields = array()) 
    {
        if (!$this->_db->insert('school_year', $fields)) 
        {
            throw new Exception('There was a problem in editting current school year, please try again later.');
        }
    }

    public function updateSchoolYear($fields = array(), $id = null) 
    {
        if (!$this->_db->update('school_year', 'sy_id', $id, $fields)) 
        {
            throw new Exception('There was a problem in updating school year settings, please try again later.');
        }
    }

    public function getStudent($id = null) 
    {
        if ($id) 
        {
            $get = $this->_db->query('SELECT * FROM view_students WHERE student_id = ?', array($id));

            if ($get->count()) 
            {
                $this->_data = $get->first();
                return true;
            }
        }
        return false;
    }

    public function getStudentByUserId($id = null) 
    {
        if ($id) 
        {
            $get = $this->_db->get('view_students', array('user_id', '=', $id));

            if ($get->count()) 
            {
                $this->_data = $get->first();
                return true;
            }
        }
        return false;
    }

    public function getStudentGrades($id) 
    {
        $get = $this->_db->query("SELECT * FROM view_student_grades WHERE student_id = ?", array($id));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getStudentSubjects($student_id, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM view_student_subject 
                                  WHERE student_id = ? 
                                    AND semester = ? 
                                    AND school_year = ?", array($student_id, $sem, $sy));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getStudentSubjectsName($student_id) 
    {
        $get = $this->_db->query("SELECT * FROM view_get_students_subject_name WHERE student_id = ?", array($student_id));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getList() 
    {
        $getList = $this->_db->query("SELECT * FROM view_students");

        if ($getList) 
        {
            if ($getList->count()) 
            {
                $this->_results = $getList->results();
                return true;
            }
        }
        return false;
    }

    public function getSubjects($sy, $sem) 
    {
        $getSubjects = $this->_db->query("SELECT * FROM view_subjects WHERE semester = ? AND school_year = ?", array($sy, $sem));

        if ($getSubjects->count()) 
        {
            $this->_results = $getSubjects->results();
            return true;
        }
        return false;
    }

    public function getStudentsNotExistsInSections($sy, $semester) 
    {
        $get = $this->_db->query("SELECT DISTINCT st.* FROM students st 
                                  WHERE NOT EXISTS ( 
                                    SELECT stc.student_id FROM student_section stc 
                                    WHERE st.student_id = stc.student_id 
                                        AND stc.school_year = ? 
                                        AND stc.semester = ?
                                    ORDER BY lastname ASC
                                  )", array($sy, $semester));

        if ($get->count()) 
        { 
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getSections() 
    {
        $getSections = $this->_db->query('SELECT * FROM view_sections');

        if ($getSections->count()) 
        {
            $this->_results = $getSections->results();
            return true;
        }
        return false;
    }

    public function getCurrentSectionStudentsList($section_id, $sy, $sem) 
    {
        $list = $this->_db->query("SELECT * FROM view_current_student_section_list 
                                    WHERE section_id = ? 
                                        AND school_year = ? 
                                        AND semester = ?", array($section_id, $sy, $sem));

        if ($list->count()) 
        {
            $this->_results = $list->results();
            return true;
        }
        return false;
    }

    public function getStudentCurrentSubjects($sid, $yr, $sem, $sy) 
    {
        $get = $this->_db->query("SELECT * FROM view_subjects_by_student WHERE student_id = ? AND year_id = ? AND semester = ? AND school_year = ?", array($sid, $yr, $sem, $sy));

        if ($get->count())
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getTotalStudentsBySection($section_id, $sy, $sem) 
    {
        $getTotal = $this->_db->query("SELECT DISTINCT COUNT(student_id) AS total_student FROM view_current_student_section_list 
                                        WHERE section_id = ? 
                                            AND school_year = ? 
                                            AND semester = ?", array($section_id, $sy, $sem));

        if ($getTotal->count()) 
        {
            $this->_count = $getTotal->first()->total_student;
            return true;
        }
        return false;
    }

    public function getYears() 
    {
        $get = $this->_db->query("SELECT * FROM year");

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function getCurrentSchoolYear() {
        $result = $this->_db->query("SELECT * FROM view_current_school_year");
        if ($result->count()) {
            $this->_results = $result->results();
            return true;
        }
        return false;
    }

    public function getStudentYear($id) 
    {
        $get = $this->_db->get('view_student_year', array('student_id', '=', $id));

        if ($get->count()) 
        {
            $this->_results = $get->results();
            return true;
        }
        return false;
    }

    public function removeStudentFromSection($section_id, $id, $sy, $sem) 
    {
        if (!$this->_db->query("DELETE FROM student_section WHERE section_id = ? AND student_id = ? AND school_year = ? AND semester = ?", array($section_id, $id, $sy, $sem))) 
        {
            throw new Exception("There was a problem in removing student from section, please try again later.");
        }
    }

    public function removeSubjectFromStudent($subject_id) 
    {
        if (!$this->_db->delete('student_subject', array('student_subject_id', '=', $subject_id))) 
        {
            throw new Exception('There was a problem in removing subject from student, please try again later.');
        }
    }

    public function checkStudentSubject($subject_id, $student_id, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM view_student_subject 
                                    WHERE subject_id = ? 
                                        AND student_id = ?
                                        AND semester = ? 
                                        AND school_year = ?", array($subject_id, $student_id, $sem, $sy));

        if ($check->count()) 
        {
            return true;
        }
        return false;
    }

    public function checkStudentYear($id, $yr, $sem, $sy) 
    {
        $check = $this->_db->query("SELECT * FROM student_year WHERE student_id = ? AND year_id = ? AND semester = ? AND school_year = ?", array($id, $yr, $sem, $sy));

        if ($check->count()) 
        {
            return true;
        }
        return false;
    }

    public function checkSchoolYear($sy, $sem) 
    {
        $check = $this->_db->query("SELECT * FROM view_school_year WHERE schoolyear = ? AND semester = ?", array($sy, $sem));

        if ($check->count()) 
        {
            $this->_results = $check->results();
            return true;
        }
        return false;
    }

    public function results() 
    {
        return $this->_results;
    }

    public function data() 
    {
        return $this->_data;
    }

    public function count() 
    {
        return $this->_count;
    }

    public function first() 
    {
        return $this->results()[0];
    }
}