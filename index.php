<?php

require_once("./function.php");
require_once("./header.php");

$cl=new Main;

if (isset($_GET["del"])){  //if delete some timetable
	$cl->delete_timetable($cl->ci($_GET["del"]));	
} elseif (isset($_POST["begin"])) { //if add some timetable
	$cl->add_timetable($_POST);
} elseif (isset($_GET["view"])) {  //view some timetable
	$cl->view_timetable($cl->ci($_GET["view"]));
} elseif (isset($_GET["add"])) {  //view form for add timetable
	$cl->add_timetable_form();
} else {  //another way - print list of timetables
	$cl->view_all_timetable();
}

require_once("./footer.php");
?>