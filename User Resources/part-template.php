<?php
/**
 * part-template.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:47
 */
define('BASEPATH', realpath(dirname(__FILE__)));

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');

?>
<div class="app-part">
    <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        MODULE NAME
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('MODULE SCRIPT NAME')) ?>
      </h3>
    </div>
    <div class="panel-body">

    </div>
  </div>
</div>