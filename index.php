<?php
namespace TymFrontiers;
require_once ".appinit.php";
require_once APP_ROOT . "/src/Helper.php";
if ($session->isLoggedIn()) {
  include "./service/index.php";
} else {
  include "./service/welcome.php";
}
?>
