<?php
    require_once "NotLogin.php";
    require_once "Header.php";
    if ($_SESSION["UserType"] != 2) {
        echo "非法调用：没有权限";
		CreateNewLine();
		require_once "Footer.php";
		die();
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    $QueryCmd = array(
        array(
            "SELECT", 
            "FROM", 
            "WHERE", 
            "INSERT", 
            "INTO", 
            "VALUES", 
            "UPDATE", 
            "SET", 
            "DELETE", 
            "WHERE", 
            "AND", 
            "OR", 
            "ORDER BY", 
            "DESCRIBE", 
            "SHOW", 
            "TABLES",
            "DATABASES",
            "USE"
        ),
        array(
            "`", 
            "(", 
            ")", 
            "*", 
            "=",
            " "
        )
    );
    for ($i = 0; $i < sizeof($QueryCmd); $i++) {
        for ($j = 0; $j < sizeof($QueryCmd[$i]); $j++) {
            echo "<input type=\"button\" class=\"SecondButton\" value=\"" . $QueryCmd[$i][$j] . "\" onclick=\"InputSearchBox.value+='" . $QueryCmd[$i][$j] . "';\" />";
        }
        CreateNewLine();
    }
    echo "<input type=\"button\" class=\"SecondButton\" value=\"←\" onclick=\"InputSearchBox.value=InputSearchBox.value.substr(0, InputSearchBox.value.length - 1);\" />";
    echo "<input type=\"button\" class=\"SecondButton\" value=\"清空\" onclick=\"InputSearchBox.value='';\" />";
    CreateNewLine();
    echo "<input style=\"width: -moz-available; width: -webkit-fill-available; \" type=\"text\" class=\"Input\" name=\"Search\" id=\"InputSearchBox\" />";
    CreateNewLine();
    echo "<input class=\"MainButton\" type=\"submit\" value=\"执行\" />";
    echo "</form>";
    if (isset($_POST["Search"]) && $_POST["Search"] != "") {
        $Result = $Connect->query($_POST["Search"]);
        if (is_object($Result)) {
            echo "<pre>";
            print_r($Result);
            echo "</pre>";
            echo "<table style=\"width: 100%\" border=\"1\">";
            echo "<tbody>";
            for ($i = 0; $i < $Result->num_rows; $i++) {
                echo "<tr>";
                $Result->data_seek($i);
                $RowData = $Result->fetch_array(MYSQLI_NUM);
                for ($j = 0; $j < $Result->field_count; $j++) {
                    echo "<td>";
                    echo SanitizeString($RowData[$j]);
                    echo "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
        else if ($Result) {
            CreateText("执行成功");
        }
        else {
            CreateText("执行失败，请检查命令是否有错误");
        }
        CreateNewLine();
        CreateNewLine();
    }
    require_once "Footer.php";
?>
