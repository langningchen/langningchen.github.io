<?php
    require_once "NotLogin.php";
    require_once "Function.php";
    if (!isset($_GET["File"])) {
        echo "非法调用：没有传入File参数";
		CreateNewLine();
        die();
    }
    if (!isset($_GET["FileName"])) {
        echo "非法调用：没有传入FileName参数";
		CreateNewLine();
        die();
    }
    if (!isset($_GET["Time"])) {
        echo "非法调用：没有传入Time参数";
		CreateNewLine();
        die();
    }
    if (!isset($_GET["Sign"])) {
        echo "非法调用：没有传入Sign参数";
		CreateNewLine();
        die();
    }
    if (strlen($_GET["File"]) % 2 != 0) {
        echo "非法调用：File参数不正确";
		CreateNewLine();
        die();
    }
    if (strlen($_GET["FileName"]) % 2 != 0) {
        echo "非法调用：FileName参数不正确";
		CreateNewLine();
        die();
    }
    $File = $_GET["File"];
    $Time = $_GET["Time"];
    $FileName = $_GET["FileName"];
    if (abs(date_timestamp_get(date_create()) - $Time) > 30 * 60) {
        echo "非法调用：Time已超时";
		CreateNewLine();
        die();
    }
    $Sign = $_GET["Sign"];
    if ($Sign != md5($File . $Time . $FileName . $_SESSION["UID"] . $_SESSION["UserName"])) {
        echo "非法调用：Sign不正确";
		CreateNewLine();
        die();
    }
    $File = hex2bin($File);
    $FileName = hex2bin($FileName);
    if (!file_exists($File)) {
        echo "非法调用：没有此文件";
		CreateNewLine();
        die();
    }
    header("Accept-Ranges: bytes");
    header("Accept-Length: " . filesize($File));
    header("Content-Transfer-Encoding: binary");
    header("Content-Disposition: attachment; filename=" . $FileName);
    header("Content-Type: application/octet-stream; name=" . $FileName);
    echo file_get_contents($File);
?>
