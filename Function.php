<?php
    $DataBaseName = "data";
    $DataBaseUserName = "Eric";
    $DataBasePassWord = "R)k7D(iDEtJs.Wr7";
    $Connect = new mysqli("", $DataBaseUserName, $DataBasePassWord, "");
    if ($Connect->connect_error) {
        echo "无法连接到数据库！";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    $Connect->query("USE " . $DataBaseName);
    function SanitizeString($String) {
        $String = stripslashes($String);
        $String = htmlentities($String);
        return $String;
    }
    function GetUserName($UID) {
        $ReturnValue = "<a class=\"";
        global $Connect;
        $Temp = $Connect->prepare("SELECT UserType FROM userlist WHERE UID=?");
        $Temp->bind_param("i", $UID);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows == 1) {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if ($RowData[0] == "0") $ReturnValue .= "OriginalUser";
            else if ($RowData[0] == "1") $ReturnValue .= "TeacherUser";
            else if ($RowData[0] == "2") $ReturnValue .= "AdminUser";
            else if ($RowData[0] == "3") $ReturnValue .= "BadUser";
        }
        $ReturnValue .= "\" href=\"User.php?UID=" . $UID . "\"><i>@" . GetNoLinkUserName($UID) . "</i></a> ";
        return $ReturnValue;
    }
    function GetNoLinkUserName($UID) {
        global $Connect;
        $Temp = $Connect->prepare("SELECT UserName FROM userlist WHERE UID=?");
        $Temp->bind_param("i", $UID);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows != 1) {
            return "没有该用户";
        }
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        return $RowData[0];
    }
    function EncodePassword($Password) {
        $Salt1 = "OG]vh]JpaAyLDGV[";
        $Salt2 = "8ev-4ekYRfa8k_96";
        return hash("ripemd128", "$Salt1$Password$Salt2");
    }
    function GetFriends() {
		$AnsArray = array();
        if ($_SESSION["UID"] != 1) {
            array_push($AnsArray, "1");
        }
        global $Connect;
        $Temp = $Connect->prepare("SELECT * FROM classlist WHERE 1");
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $Member = "";
            if (!in_array($RowData[2], $AnsArray)) {
                array_push($AnsArray, $RowData[2]);
            }
            $TempArray = StringToArray($RowData[3]);
            for ($i = 0; $i < count($TempArray); $i++) {
                if (!in_array($TempArray[$i], $AnsArray)) {
                    array_push($AnsArray, $TempArray[$i]);
                }
            }
            if ($_SESSION["UserType"] != 0) {
				$TempArray = StringToArray($RowData[4]);
				for ($i = 0; $i < count($TempArray); $i++) {
					if (!in_array($TempArray[$i], $AnsArray)) {
						array_push($AnsArray, $TempArray[$i]);
					}
                }
            }
        }
        sort($AnsArray);
        return $AnsArray;
    }
    function GetClassType($ClassID) {
        global $Connect;
        $Temp = $Connect->prepare("SELECT * FROM classlist WHERE ClassID=?");
        $Temp->bind_param("i", $ClassID);
        $Temp->execute();
        $Result = $Temp->get_result();
        if ($Result->num_rows != 1) {
            return "";
        }
        if ($_SESSION["UserType"] == 2) {
            return "超级管理员";
        }
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $Member = "";
        if ($RowData[2] == $_SESSION["UID"]) {
            $Member = "管理员";
        }
        else if (in_array($_SESSION["UID"], StringToArray($RowData[3]))) {
            $Member = "教师";
        }
        else if (in_array($_SESSION["UID"], StringToArray($RowData[4]))) {
            $Member = "学生";
        }
        return $Member;
    }
	function StringToArray($s) {
		$Ans = array();
		$Index = strpos($s, ",");
		$LastIndex = 0;
		while($LastIndex < strlen($s)) {
            array_push($Ans, substr($s, $LastIndex, $Index - $LastIndex));
			$LastIndex = $Index + 1;
			if ($LastIndex < strlen($s)) {
				$Index = strpos($s, ",", $LastIndex);
			}
		}
		return $Ans;
	}
    function CreateText($Value) {
        echo "<span class=\"Text\">" . $Value . "</span>";
    }
    function CreateLink($Href, $Value) {
        echo "<a href=\"" . $Href . "\" class=\"Link\">" . $Value . "</a>";
    }
    function CreateNewLine() {
        echo "<br />";
    }
    function CreateDownload($File, $FileName, $Value = "下载") {
        $File = bin2hex($File);
        $FileName = bin2hex($FileName);
        $Time = date_timestamp_get(date_create());
        $Sign = md5($File . $Time . $FileName . $_SESSION["UID"] . $_SESSION["UserName"]);
        echo "<input class=\"SecondButton\" type=\"button\" onclick=\"window.open('Download.php?File=" . $File . "&Time=" . $Time . "&FileName=" . $FileName . "&Sign=" . $Sign . "')\" value=\"" . $Value . "\" />";
    }
    function GetHomeworkStatusName($Status) {
        if ($Status == 0) {
            return "未提交";
        }
        if ($Status == 1) {
            return "提交未批改";
        }
        if ($Status == 2) {
            return "需订正";
        }
        if ($Status == 3) {
            return "订正未批改";
        }
        if ($Status == 4) {
            return "通过";
        }
        if ($Status == 5) {
            return "优秀";
        }
    }
    function GetClockInStatusName($Status) {
        if ($Status == 0) {
            return "未打卡";
        }
        if ($Status == 1) {
            return "已打卡";
        }
        if ($Status == 3) {
            return "打卡优秀";
        }
    }
    function DeleteClockIn($ClockInID) {
        global $Connect;
        $Temp = $Connect->prepare("DELETE FROM clockinlist WHERE ClockInID=?");
        $Temp->bind_param("i", $ClockInID);
        $Temp->execute();

        $Temp = $Connect->prepare("SELECT ClockInUploadID FROM clockinuploadlist WHERE ClockInID=?");
        $Temp->bind_param("i", $ClockInID);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $DeleteTemp = $Connect->prepare("DELETE FROM clockinuploadchecklist WHERE ClockInUploadID=?");
            $DeleteTemp->bind_param("i", $RowData[0]);
            $DeleteTemp->execute();
        }
        $Temp = $Connect->prepare("DELETE FROM clockinuploadlist WHERE ClockInID=?");
        $Temp->bind_param("i", $ClockInID);
        $Temp->execute();

        $Temp = $Connect->prepare("SELECT ClockInUploadFileID, UploadUID, FileName FROM clockinuploadfilelist WHERE ClockInID=?");
        $Temp->bind_param("i", $ClockInID);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if (file_exists("ClockInUploadFile/" . $Result[0] . "_" . $ClockInID . "_" . $Result[1] . "_" . $RowData[2])) unlink("ClockInUploadFile/" . $Result[0] . "_" . $Result[1] . "_" . $Result[2] . "_" . $RowData[3]);
        }
        $Temp = $Connect->prepare("DELETE FROM clockinuploadfilelist WHERE ClockInID=?");
        $Temp->bind_param("i", $ClockInID);
        $Temp->execute();
    }
    function DeleteHomework($HomeworkID) {
        global $Connect;
        $Temp = $Connect->prepare("DELETE FROM Homeworklist WHERE HomeworkID=?");
        $Temp->bind_param("i", $HomeworkID);
        $Temp->execute();

        $Temp = $Connect->prepare("SELECT HomeworkUploadID FROM Homeworkuploadlist WHERE HomeworkID=?");
        $Temp->bind_param("i", $HomeworkID);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);

            $DeleteTemp = $Connect->prepare("SELECT HomeworkUploadCheckID, FileName FROM Homeworkuploadchecklist WHERE HomeworkUploadID=?");
            $DeleteTemp->bind_param("i", $RowData[0]);
            $DeleteTemp->execute();
            $DeleteResult = $DeleteTemp->get_result();
            for ($j = 0; $j < $Result->num_rows; $j++) {
                $DeleteResult->data_seek($j);
                $DeleteRowData = $DeleteResult->fetch_array(MYSQLI_NUM);
                if (file_exists("HomeworkUploadCheckFile/" . $DeleteRowData[0] . "_" . $DeleteRowData[1])) unlink("HomeworkUploadCheckFile/" . $DeleteRowData[0] . "_" . $DeleteRowData[1]);
            }
            $DeleteTemp = $Connect->prepare("DELETE FROM Homeworkuploadchecklist WHERE HomeworkUploadID=?");
            $DeleteTemp->bind_param("i", $RowData[0]);
            $DeleteTemp->execute();

            $DeleteTemp = $Connect->prepare("DELETE FROM Homeworkuploadchecklist WHERE HomeworkUploadID=?");
            $DeleteTemp->bind_param("i", $RowData[0]);
            $DeleteTemp->execute();
        }
        $Temp = $Connect->prepare("DELETE FROM Homeworkuploadlist WHERE HomeworkID=?");
        $Temp->bind_param("i", $HomeworkID);
        $Temp->execute();

        $Temp = $Connect->prepare("SELECT HomeworkUploadFileID, UploadUID, FileName FROM Homeworkuploadfilelist WHERE HomeworkID=?");
        $Temp->bind_param("i", $HomeworkID);
        $Temp->execute();
        $Result = $Temp->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if (file_exists("HomeworkUploadFile/" . $Result[0] . "_" . $HomeworkID . "_" . $Result[1] . "_" . $RowData[2])) unlink("HomeworkUploadFile/" . $Result[0] . "_" . $Result[1] . "_" . $Result[2] . "_" . $RowData[3]);
        }
        $Temp = $Connect->prepare("DELETE FROM Homeworkuploadfilelist WHERE HomeworkID=?");
        $Temp->bind_param("i", $HomeworkID);
        $Temp->execute();
    }
    function CreateRandPassword() {
        $RandPassword = "";
        $CharSet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()";
        for ($i = 0; $i < 16; $i++) {
            $RandPassword .= substr($CharSet, rand(0, strlen($CharSet) - 1), 1);
        }
        return $RandPassword;
    }
?>
