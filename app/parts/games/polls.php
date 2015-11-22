<?php
/**
 * polls.php
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

?>
<div class="app-part">
    <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Poll System
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('pollSystem.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Start A New Vote</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('poll open', 'Start a normal poll', 'option1 option2...') ?>
          <div class="form-group">
            <?= $templates->botCommandButton('poll close', 'End Current Poll') ?>
          </div>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('poll open -t', 'Start a timed poll', 'seconds option1 option2...') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('To vote type <b>!vote "option"</b> in chat.<br />You can enter more than 2 options if needed.')?>
        </div>
      </div>
      <hr/>
      <h4>Poll Results</h4>
      <?= $templates->botCommandButton('poll results', 'Announce Last Poll Results') ?>
    </div>
  </div>
</div>