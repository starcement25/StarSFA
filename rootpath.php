<?php
echo "/usr/local/bin/php /home/acedns/public_html/CI/index.php /Update_api_key/key_update";
echo "<pre>";
echo exec('whereis php');
echo "</pre>";echo "<br>";
echo $_SERVER['PATH'];echo "<br>";
echo $dir = dirname(__FILE__);echo "<br>";
echo phpinfo();
?>