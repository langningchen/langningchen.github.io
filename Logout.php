<?php
    session_start();
    $_SESSION["UID"] = "";
    $_SESSION["UserName"] = "";
    $_SESSION["UserType"] = "";
    session_destroy();
    setcookie("UserName", "", time() - 1);
    setcookie("Password", "", time() - 1);
    setcookie("UID", "", time() - 1);
    setcookie("UserType", "", time() - 1);
    require_once "Header.php";
    echo "<script>window.location=\"login.php\"</script>";
    require_once "footer.php";
?>
