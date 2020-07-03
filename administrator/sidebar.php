<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="ml-2 text-center the-title">Student Management System </h3>
        <br>
    </div>
    <ul class="list-unstyled components">
        <span class="text-center">
            <h6>WELCOME,</h6>
            <h5>ADMINISTRATOR</h5>
        </span>
        <hr>
        <li>
            <a href="./"><i class="ml-2 fas fa-tachometer-alt"></i> &nbsp;Home</a>
        </li>
        <li>
            <a class="dropdown-toggle" href="#students" data-toggle="collapse" aria-expanded="false"><i class="ml-2 fas fa-user-graduate"></i> &nbsp;Students</a>
            <ul class="collapse list-unstyled" id="students">
                <li>
                    <a href="students.php"><i class="fas fa-minus mr-2"></i>&nbsp;Students Information</a>
                </li>
                <li>
                    <a href="student.php"><i class="fas fa-minus mr-2"></i>&nbsp;Add Student</a>
                </li>
                <li>
                    <a class="dropdown-toggle" href="#_sections" data-toggle="collapse" aria-expanded="false"><i class="fas fa-minus mr-2"></i>&nbsp;Sections</a>
                    <ul class="collapse list-unstyled" id="_sections">
                        <li>
                            <a href="sections.php"><i class="ml-3 fas fa-minus mr-2"></i>&nbsp;View Sections</a>
                        </li>
                        <li>
                            <a href="add-students-to-section.php"><i class="ml-3 fas fa-minus mr-2"></i>&nbsp;Add Students to Section</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="dropdown-toggle" href="#_subjects" data-toggle="collapse" aria-expanded="false"><i class="fas fa-minus mr-2"></i>&nbsp;Subjects</a>
                    <ul class="collapse list-unstyled" id="_subjects">
                        <li>
                            <a href="add-subject.php"><i class="ml-3 fas fa-minus mr-2"></i>&nbsp;Add Subject</a>
                        </li>
                        <li>
                            <a href="subjects.php"><i class="ml-3 fas fa-minus mr-2"></i>&nbsp;View Subjects</a>
                        </li>
                        <li>
                            <a href="assign-subjects.php"><i class="ml-3 fas fa-minus mr-2"></i>&nbsp;Assign Subjects</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <a class="dropdown-toggle" href="#Instructors" data-toggle="collapse" aria-expanded="false"><i class="ml-2 fas fa-users"></i> &nbsp;Instructors</a>
            <ul class="collapse list-unstyled" id="Instructors">
                <li>
                    <a href="instructor.php"><i class="fas fa-minus mr-2"></i>&nbsp;Add Instructor</a>
                </li>
                <li>
                    <a href="instructors-list.php"><i class="fas fa-minus mr-2"></i>&nbsp;Instructors List</a>
                </li>
                <li>
                    <a href="assign-subjects_instructor.php"><i class="fas fa-minus mr-2"></i>&nbsp;Assign Subjects</a>
                </li>
            </ul>
        </li>
        <li>
            <a class="dropdown-toggle" href="#users" data-toggle="collapse" aria-expanded="false"><i class="ml-2 fas fa-user-secret"></i> &nbsp;User Management</a>
            <ul class="collapse list-unstyled" id="users">
                <li>
                    <a href="add-admin.php"><i class="fas fa-minus mr-2"></i>&nbsp;Add Administrator</a>
                </li>
                <li>
                    <a href="view-admins.php"><i class="fas fa-minus mr-2"></i>&nbsp;View Administrators List</a>
                </li>
            </ul>
        </li>
    </ul>
    <br><br><br><br><br><br><br><br><br>
</nav>