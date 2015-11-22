<?php
/**
 * roll.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:41
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

$botSettings = $functions->getIniArray('settings');
$rollSettings = $functions->getIniArray('roll');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Roll Command
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('rollCommand.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?= $templates->botCommandButton('roll', 'Make A Roll') ?>
        <?=$templates->switchToggle('Toggle Cool Down', 'doQuickCommand', '[\'roll wait\']',
            null, (array_key_exists('roll_wait', $rollSettings) && filter_var($rollSettings['roll_wait'], FILTER_VALIDATE_BOOLEAN)))?>
        <?=$templates->switchToggle('Toggle Stream-Online-Only', 'doQuickCommand', '[\'roll stream\']',
            null, (array_key_exists('roll_stream', $rollSettings) && filter_var($rollSettings['roll_stream'], FILTER_VALIDATE_BOOLEAN)))?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Roll Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('roll time', 'Set Roll Cool Down', 'seconds', (array_key_exists('roll_timer', $rollSettings) ? $rollSettings['roll_timer'] : '')) ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('roll bonus', 'Set Roll Doubles Multiplier', 'amount', (array_key_exists('roll_bonus', $rollSettings) ? $rollSettings['roll_bonus'] : '')) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>