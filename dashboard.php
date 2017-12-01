<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Control Panel</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

    <!-- Datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="http://www.theadriangee.com/aw-cpanel/assets/css/custom.css">

    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

    <!--jQuery datatable -->
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Font Awesome library -->
    <script src="https://use.fontawesome.com/a2605d050c.js"></script>

    <!-- Custom JS -->
    <script src="http://www.theadriangee.com/aw-cpanel/assets/js/custom.js"></script>


</head>
<body>
<?php

require_once './classes/Dashboard.php';
$ds = new Dashboard();

if ($_POST) {
    $status = $ds->authorize($_POST['username'], $_POST['password']);
    if ($status > 0) {
        $_SESSION['current_user'] = $_POST['username'];
        $page = $ds->get_dashboard_panel();
        echo $page;
    } // end if $status > 0
    else {
        header("Location: $ds->index_page");
    } // end else
}  // end if $_POST
else {
    header("Location: $ds->index_page");
}

?>


<script type="text/javascript">


</script>

</body>
</html>
