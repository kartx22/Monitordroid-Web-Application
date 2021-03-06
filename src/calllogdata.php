<?php
if(!isset($_SESSION)) session_start();

    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

    $email = $_SESSION['email'];
    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    include_once '../db_functions.php';

    $db = new DB_Functions();

    if (! $db->userDeviceVerify($rowId, $email)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit();
    }

    $updTime = 1;
    $updTime = $db->getCallLogsUpdateTime( $reg );

    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");

	$calllog = $db->getCallLogs($reg);

	$mxr = explode("}", $calllog);
	$paragraphs = $mxr[0];
	for ($i = 1; $i < count($mxr) - 1; $i += 1) {
		$paragraphs .= '},' . $mxr[$i];
	}
	if ($paragraphs != "") {
		$paragraphs .= '}';
	}

	if ($paragraphs == "") {
		echo "[]";
	}
	else {
		echo '[' . stripslashes($paragraphs) . ']';
	}
    ?>
