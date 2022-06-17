<?php
    if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"])) {
        CreateNewLine();
        echo "<footer>";
        echo "Developed by Langning Chen. All rights reserved.";
        echo "</footer>";
    }
    echo "</body>";
    echo "</html>";
?>
