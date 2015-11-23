<?php
/**
 * phrases-trigger.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:43
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');
require_once(BASEPATH . '/app/php/classes/ComponentTemplates.class.php');
require_once(BASEPATH . '/app/php/classes/PanelSession.class.php');


$session = new PanelSession($_COOKIE['PHPSESSID']);
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
  die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$phrases = $functions->getIniArray('phrases');
$phraseTableRows = '';

foreach ($phrases as $emote => $reply) {
  $phraseTableRows .= '<tr><td>' . $emote . '</td><td>' . $reply . '</td></tr>';
}


?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Phrase Triggers
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('phraseHandler.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Phrase Trigger Settings</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('addphrase', 'Add reply', 'emote reply') ?>
          <?= $templates->botCommandForm('delphrase', 'Delete reply', 'emote') ?>
        </div>
        <div class="col-sm-4 col-sm-offset-4">
          <?= $templates->informationPanel('The bot can reply to words posted in the chat.<br/>"emote" equals to one word without spaces!') ?>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Current Phrase Triggers', ['Emote', 'Reply'], $phraseTableRows, true) ?>
    </div>
  </div>
</div>