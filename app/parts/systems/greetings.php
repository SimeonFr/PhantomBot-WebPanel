<?php
/**
 * greetings.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:42
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$greetingSettings = $functions->getIniArray('greeting');
$userGreetingRows = '';

foreach ($greetingSettings as $username => $greeting) {
  if (!in_array($username, ['autogreet', '_default'])) {
    $userGreetingRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $greeting . '</td></tr>';
  }
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Greeting System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('greetingSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <?= $templates->switchToggle('Toggle Auto-Greeting', 'doQuickCommand', '[\'greeting toggle\']',
          null, (array_key_exists('autogreet', $greetingSettings) && filter_var($greetingSettings['autogreet'], FILTER_VALIDATE_BOOLEAN))) ?>
      <hr/>
      <h4>Greeting Message</h4>

      <div class="row">
        <div class="col-sm-6">
          <?= $templates->botCommandForm('greeting setdefault', 'Set global greeting message', 'message', (array_key_exists('_default', $greetingSettings) ? $greetingSettings['_default'] : '')) ?>
        </div>
        <div class="col-sm-4 col-sm-offset-2">
          <?= $templates->informationPanel('Users can set their own greeting using "!greeting set &lt;message&gt;". Then enable/disable the greeting by using "!greeting &lt;enable/disable&gt;".') ?>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('User Greetings', ['Username', 'Greeting'], $userGreetingRows, true) ?>
    </div>
  </div>
</div>