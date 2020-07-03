<?php
 require 'school-year.php'; ?>
<nav class="navbar navbar-expand-lg bg-green">
    <div class="container-fluid">

        <button type="button" id="sidebarCollapse" class="btn bg-green">
            <i class="fas fa-align-left text-light"></i>
            <span>Toggle Sidebar</span>
        </button>
        &nbsp;&nbsp;
        <span class="text-light text-center"><?php echo (isset($school_year) ? 'S.Y. ' . $school_year : '') . ' | ' . (isset($semester) ? $semester : ''); ?></script></span>
        <button class="btn bg-green text-light d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-align-justify"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-light text-right" href="account_settings.php">Account Settings <i class="fas fa-cogs"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light text-right" href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>