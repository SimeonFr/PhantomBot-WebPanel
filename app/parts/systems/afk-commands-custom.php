<?php
/**
 * phrases-trigger.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:43
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Configuration.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ConnectionHandler.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Functions.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$afkUsers = $functions->getIniArray('afk_users');
$afkUsersTime = $functions->getIniArray('afk_users_time');
$afkUsersTableRows = '';

foreach ($afkUsers as $username => $status) {
  $timeSince = intval($afkUsersTime[$username]);
  $status = ($status == '1' ? 'AFK' : 'Lurking');
  $afkUsersTableRows .= '<tr><td>' . $username . '</td><td>' . $status . '</td><td>' . $functions->secondsToTime($timeSince / 1000) . '</td></tr>';
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Afk Command
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('afkCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <?=$templates->informationPanel('<p>This module does not have any settings.</p>
          <p>Users can set their AFK/lurk status by using either "!afk" or "!lurk". This status will be terminated when they post a message in the chat.</p>', true)?>
      <hr/>
      <?= $templates->dataTable('Current Afk/Lurking users', ['Username', 'Status'], $afkUsersTableRows, true) ?>
    </div>
  </div>
</div>