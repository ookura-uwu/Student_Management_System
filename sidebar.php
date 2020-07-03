<?php

require_once 'core/init.php';

$location = basename($_SERVER['REQUEST_URI']);

if ($_GET) 
{
    $parsed = parse_url($location);
    $query = $parsed['query'];

    parse_str($query, $params);

    $string = http_build_query($params);

    $baseLocation = str_replace('%20', '+', $location);
    $baseLocation = str_replace('?' . $string, '', $baseLocation);
} 
else 
{
    $baseLocation = $location;
}
?>
<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="ml-2 text-center the-title">Student Management System</h3>
        <br>
    </div>

    <ul class="list-unstyled components">
        <span class="text-center">
            <h6><?php echo strtoupper($data->lastname) . ', ' . $data->firstname ?></h6>
            <br>
            <h6 class="ml-2 mr-2 card-subtitle text-muted text-center"><?php echo $data->program ?></h6>
        </span>
        <br>
        <li class="<?php echo ($baseLocation == 'home.php' ? 'active' : '') ?>">
            <a href="home.php" ><i class="ml-2 fas fa-home"></i> &nbsp;Home</a>
        </li>
        <li class="<?php ($baseLocation == 'view-grades.php' ? print 'active' : '') ?>">
            <a href="view-grades.php"><i class="ml-2 fas fa-book-open"></i> &nbsp;Grades</a>
        </li>
    </ul>
</nav>