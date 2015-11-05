<?php
/**
 * chat-settings.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:39
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Configuration.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ConnectionHandler.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/Functions.class.php');
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/php/classes/ComponentTemplates.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$templates = new ComponentTemplates();

$botSettings = $functions->getIniArray('settings');

?>
<script>
  toggleStreamPreview(pBotData.streamPreviewActive)
</script>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Settings & Stream Preview
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Add/Remove Secondary Chat</h4>

      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <div class="input-group">
              <input id="second-chat-form-input" class="form-control" placeholder="username"/>
            <span class="input-group-btn">
              <button class="btn btn-primary" onclick="addSecondChat()">Set</button>
            </span>
            <span class="input-group-btn">
              <button class="btn btn-danger" onclick="clearSecondChat()">Close</button>
            </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <?= $templates->informationPanel('<p>You can have two chats show in the side bar.<br />Quite handy when you\'re streaming in co-op and need to keep track of two chats!</p>
      <p>Enable the chat in Extras->Enable Chat, enter a username below and click "Set".<br />You can close the second chat at any time by clicking "Close".</p>
      <p>You can safely browse around the panel. The chats are there to stay, as long as you keep them enabled.</p>') ?>
        </div>
      </div>
      <hr/>
      <h4>Stream Preview</h4>

      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Stream Preview', 'toggleStreamPreview', null, 'toggle-stream-preview') ?>
        <button class="btn btn-default" id="stream-preview-fit-video" onclick="calculateStreamHeight()">Re-Fit Video</button>
      </div>
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1">
          <iframe id="stream-preview" src=""></iframe>
        </div>
      </div>
    </div>
  </div>
</div>