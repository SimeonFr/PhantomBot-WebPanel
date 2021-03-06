<?php
/**
 * follower-events.php
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
require_once(BASEPATH . '/app/php/classes/PanelSession.class.php');


$session = new PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$recordedFollows = $functions->getIniArray('followed');
$userLastSeen = $functions->getIniArray('lastseen');
$followersTableRows = '';

foreach ($recordedFollows as $username => $follows) {
  if ($follows == '1') {
    $followersTableRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . (array_key_exists($username, $userLastSeen) ? $functions->botTimeToStandardFormat($userLastSeen[$username]) : 'No data yet!') . '</td></tr>';
  }
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Follow Events
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('followHandler.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('followed', 'Check follower') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('lastseen', 'Last seen') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Follower Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('followreward', 'Points reward on follow', '[amount]', (array_key_exists('followreward', $botSettings) ? $botSettings['followreward'] : '100')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Recorded Follows (' . count($recordedFollows) . ')', ['Username', 'Last Seen'], $followersTableRows, true) ?>
    </div>
  </div>
</div>