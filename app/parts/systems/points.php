<?php
/**
 * points.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:44
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

$filter = filter_input_array(INPUT_POST);
$botSettings = $functions->getIniArray('settings');
$points = $functions->getIniArray('points');
$pointsTableRows = '';

if (!is_array($filter)) {
  $filter = [];
}
if (array_key_exists('points', $filter)) {
  if ($filter['points'] == 'MAX') {
    $points = array_filter($points, function ($value) {
      return (intval($value) > 1500);
    });
  }
  if ($filter['points'] == 'ASC') {
    asort($points, SORT_NATURAL);
  }
  if ($filter['points'] == 'DESC') {
    arsort($points, SORT_NATURAL);
  }
}
if (array_key_exists('username', $filter)) {
  if ($filter['username'] == 'DESC') {
    krsort($points, SORT_NATURAL);
  }
}


foreach ($points as $username => $amount) {
  $pointsTableRows .= '<tr><td>' . ucfirst($username) . '</td><td>' . $amount . '</td></tr>';
}
?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Points System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('pointSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Command permissions for Moderators', 'doQuickCommand', '[\'Toggle Point System\']',
            null, (array_key_exists('permTogglePoints', $botSettings) && filter_var($botSettings['permTogglePoints'], FILTER_VALIDATE_BOOLEAN))) ?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Transactions</h4>

      <div class="collapsible-content">
        <p>Deposit, withdraw or set the amount of points for viewers</p>

        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points all', 'Send an amount of points to everyone', 'amount') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('letitrain', 'Divide an amount of points randomly over current viewers', 'amount') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points add', 'Send an amount of points to viewer', 'username amount') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points take', 'Take an amount of points from viewer', 'username amount') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points set', 'set viewer points to amount', 'username amount') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Currency Control</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points name single', 'Set points name (singular)', 'name', (array_key_exists('pointNameSingle', $botSettings) ? $botSettings['pointNameSingle'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points name multiple', 'Set points name (plural)', 'name', (array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points gain', 'Points gained per interval <span class="text-success">(Online)</span>', 'amount', (array_key_exists('pointGain', $botSettings) ? $botSettings['pointGain'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points offlinegain', 'Points gained per interval <span class="text-danger">(Offline)</span>', 'amount', (array_key_exists('pointGainOffline', $botSettings) ? $botSettings['pointGainOffline'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points bonus', 'Bonus per group level', 'amount', (array_key_exists('pointBonus', $botSettings) ? $botSettings['pointBonus'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points interval', 'Point gain interval <span class="text-success">(Online)</span>', 'minuted', (array_key_exists('pointInterval', $botSettings) ? $botSettings['pointInterval'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points offlineinterval', 'Point gain interval <span class="text-danger">(Offline)</span>', 'minutes', (array_key_exists('pointIntervalOffline', $botSettings) ? $botSettings['pointIntervalOffline'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('points mingift', 'Minimal gift', 'amount', (array_key_exists('pointGiftMin', $botSettings) ? $botSettings['pointGiftMin'] : '')) ?>
          </div>
        </div>
      </div>
      <hr/>
      <?= $templates->dataTable('Viewer Points (' . count($points) . ')', ['Username', 'Amount'], $pointsTableRows, true, '', [
          [
              'display' => 'Sort Username a-z',
              'name'    => 'username',
              'value'   => 'ASC',
              'active'  => (array_key_exists('username', $filter) && $filter['username'] == 'ASC'),
          ],
          [
              'display' => 'Sort Username z-a',
              'name'    => 'username',
              'value'   => 'DESC',
              'active'  => (array_key_exists('username', $filter) && $filter['username'] == 'DESC'),
          ],
          [
              'display' => 'Sort ' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points') . ' Ascending',
              'name'    => 'points',
              'value'   => 'ASC',
              'active'  => (array_key_exists('points', $filter) && $filter['points'] == 'ASC'),
          ],
          [
              'display' => 'Sort ' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points') . ' Descending',
              'name'    => 'points',
              'value'   => 'DESC',
              'active'  => (array_key_exists('points', $filter) && $filter['points'] == 'DESC'),
          ],
          [
              'display' => 'Show > 1500' . ucfirst(array_key_exists('pointNameMultiple', $botSettings) ? $botSettings['pointNameMultiple'] : 'points'),
              'name'    => 'points',
              'value'   => 'MAX',
              'active'  => (array_key_exists('points', $filter) && $filter['points'] == 'MAX'),
          ],
      ]) ?>
    </div>
  </div>
</div>