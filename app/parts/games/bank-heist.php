<?php
/**
 * bank-heist.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../../..');

require_once BASEPATH . '/app/php/classes/Configuration.class.php';
require_once BASEPATH . '/app/php/classes/ConnectionHandler.class.php';
require_once BASEPATH . '/app/php/classes/Functions.class.php';
require_once BASEPATH . '/app/php/classes/ComponentTemplates.class.php';
require_once BASEPATH . '/app/php/classes/PanelSession.class.php';

$session = new PanelSession();
if (!$session->checkSessionToken(filter_input(INPUT_POST, 'token'))) {
	die('Invalid session token. Are you trying to hack me?!');
}

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');
$bankHeistTimers = $functions->getIniArray('bankheist_timers', true);
$bankHeistStrings = $functions->getIniArray('bankheist_strings', true);
$bankHeistRatios = $functions->getIniArray('bankheist_ratios', true);
$bankHeistChances = $functions->getIniArray('bankheist_chances', true);

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Bank Heist
        <?=$templates->toggleFavoriteButton()?>
        <?=$templates->moduleActiveIndicator($functions->getModuleStatus('bankHeistSystem.js'))?>
    </div>
    <div class="panel-body">
      <div class="btn-toolbar">
        <?=$templates->botCommandButton('bankheist start', 'Start Bank Heist')?>
        <?=$templates->switchToggle('Toggle Bankheist', 'doQuickCommand', '[\'bankheist toggle\']')?>
      </div>
      <hr/>
      <h4 class="collapsible-master">Session Settings</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist signupminutes', 'Sign up time', 'minutes', (array_key_exists('signupMinutes', $bankHeistTimers) ? $bankHeistTimers['signupMinutes'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist heistminutes', 'Session Length', 'minutes', (array_key_exists('heistMinutes', $bankHeistTimers) ? $bankHeistTimers['heistMinutes'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist maxbet', 'Maximum Bet', 'minutes', (array_key_exists('bankheistmaxbet', $botSettings) ? $botSettings['bankheistmaxbet'] : ''))?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Chances</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist chances50', '50% Chance', 'percent', (array_key_exists('chances50', $bankHeistChances) ? $bankHeistChances['chances50'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist chances40', '40% Chance', 'percent', (array_key_exists('chances40', $bankHeistChances) ? $bankHeistChances['chances40'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist chances30', '30% Chance', 'percent', (array_key_exists('chances30', $bankHeistChances) ? $bankHeistChances['chances30'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist chances20', '20% Chance', 'percent', (array_key_exists('chances20', $bankHeistChances) ? $bankHeistChances['chances20'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist chances10', '10% Chance', 'percent', (array_key_exists('chances10', $bankHeistChances) ? $bankHeistChances['chances10'] : ''))?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Result Messages</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist ratio50', '50% Message', 'message', (array_key_exists('ratio50', $bankHeistRatios) ? $bankHeistRatios['ratio50'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist ratio40', '40% Message', 'message', (array_key_exists('ratio40', $bankHeistRatios) ? $bankHeistRatios['ratio40'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist ratio30', '30% Message', 'message', (array_key_exists('ratio30', $bankHeistRatios) ? $bankHeistRatios['ratio30'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist ratio20', '20% Message', 'message', (array_key_exists('ratio20', $bankHeistRatios) ? $bankHeistRatios['ratio20'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist ratio10', '10% Message', 'message', (array_key_exists('ratio10', $bankHeistRatios) ? $bankHeistRatios['ratio10'] : ''))?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Other Messages:</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist banksopen', 'Banks are open message', 'message', (array_key_exists('banksOpen', $bankHeistStrings) ? $bankHeistStrings['banksOpen'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist stringnumberinvolved', 'Player amount message', 'message', (array_key_exists('stringNumberInvolved', $bankHeistStrings) ? $bankHeistStrings['stringNumberInvolved'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist startedheist', 'Heist started message', 'message', (array_key_exists('startedHeist', $bankHeistStrings) ? $bankHeistStrings['startedHeist'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist stringstarting', 'Heist starting message', 'message', (array_key_exists('stringStarting', $bankHeistStrings) ? $bankHeistStrings['stringStarting'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist stringnojoin', 'No players joined message', 'message', (array_key_exists('stringNoJoin', $bankHeistStrings) ? $bankHeistStrings['stringNoJoin'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist entrytimeend', 'Signup time ended message', 'message', (array_key_exists('entryTimeEnd', $bankHeistStrings) ? $bankHeistStrings['entryTimeEnd'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist banksclosed', 'Banks are closed message', 'message', (array_key_exists('banksClosed', $bankHeistStrings) ? $bankHeistStrings['banksClosed'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist stringalldead', 'Everyone lost message', 'message', (array_key_exists('stringAllDead', $bankHeistStrings) ? $bankHeistStrings['stringAllDead'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist affordbet', 'Player cannot afford bet message', 'message', (array_key_exists('affordBet', $bankHeistStrings) ? $bankHeistStrings['affordBet'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist joinedheist', 'Player joined message', 'message', (array_key_exists('joinedHeist', $bankHeistStrings) ? $bankHeistStrings['joinedHeist'] : ''))?>
          </div>
          <div class="col-sm-4">
            <?=$templates->botCommandForm('bankheist stringsurvivorsare', 'Survivors message', 'message', (array_key_exists('stringSurvivorsAre', $bankHeistStrings) ? $bankHeistStrings['stringSurvivorsAre'] : ''))?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>