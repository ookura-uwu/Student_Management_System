<?php
 include 'header.php'; ?>
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

                <h4>Schedule</h4>
                <hr>
                <div class="container">
                    <table id="instructor_schedule">
                        <thead>
                            <th>Subject Code</th>
                            <th>Schedule</th>
                        </thead>
                        <tbody class="c-green">

                            <?php
                            if (isset($semester) && isset($school_year)) 
                            {
                                $get = DB::getInstance()->query("SELECT * FROM view_instructor_schedule WHERE instructor_id = ? AND semester = ? AND school_year = ?", array($data->instructor_id, $semester, $school_year));
                                if ($get->count()) 
                                {
                                    $count = 0;
                                    foreach($get->results() as $row) {
                                        $code = str_replace(' ', '', $row->subject_code);

                                        if ($count % 2 == 0) {
                                            echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border row-gray'>";
                                        } else {
                                            echo "<tr data-toggle='collapse' data-target='.{$code}' class='accordion-toggle bottom_border'>";
                                        }

                                        if ($row->class_days == 'Mon/Tue/Wed/Thur/Fri') {
                                            $class_days = 'Mon - Fri';
                                        } else if ($row->class_days == 'Mon/Tue/Wed/Thur/Fri/Sat') {
                                            $class_days = 'Mon - Sat';
                                        } else {
                                            $class_days = $row->class_days;
                                        }

                                        echo "<td>{$code}</td>";
                                        echo "<td>{$class_days} | {$row->clsTime}</td>";
                                        echo '</tr>';

                                        // Child row data
                                        echo '<tr class="child-color">';
                                        echo '<td class="text-center">';
                                        echo "<div class='accordian-body collapse {$code} child-color nopadding'><span class='ml-4'>Section:</span></div>";
                                        echo "<div class='accordian-body collapse {$code} child-color'><span class='ml-4'>Units:</span></div>";
                                        echo '</td>';

                                        echo '<td class="text-center">';
                                        echo "<div class='accordian-body collapse {$code} child-color'>{$row->section_name}</div>";
                                        echo "<div class='accordian-body collapse {$code} child-color'>{$row->units}</div>";
                                        echo '</td>';
                                        echo '</tr>';

                                        $count++;
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include 'jsplugins.php' ?>
</body>
</html