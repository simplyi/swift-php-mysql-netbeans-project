<?php
require("MyUtils.php");

echo MyUtils::generateTokenString(50);

echo "<br/>";
echo htmlentities($_GET['test']);
?>
