<?php
/**
 * preferences.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:40
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
$theme = (array_key_exists('theme', $config->paths) ? $config->paths['theme'] : '');

?>
<script>
  $('#toggle-information-button').prop('checked', pBotStorage.get(pBotStorage.keys.informationActive, true));
  $('#toggle-tooltips-button').prop('checked', !pBotStorage.get(pBotStorage.keys.tooltipsActive, false));
  $('#toggle-chat-default-button').prop('checked', pBotStorage.get(pBotStorage.keys.chatDefaultState, false));
</script>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        PhantomBot Panel Preferences
        <?= $templates->toggleFavoriteButton() ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>User Interface</h4>

      <div class="btn-toolbar">
        <?= $templates->switchToggle('Show Information Panels', 'toggleInformationPanels', '[false]', 'toggle-information-button') ?>
        <?= $templates->switchToggle('Show Tooltips', 'toggleTooltips', '[false]', 'toggle-tooltips-button') ?>
        <?= $templates->switchToggle('Show Chat By Default', 'toggleChatDefaultState', '[false]', 'toggle-chat-default-button') ?>
      </div>
      <div class="spacer"></div>
      <div class="-align-right"></div>
      <hr/>
      <h4>Misc PhantomBot Settings</h4>

      <div class="row">
        <div class="col-sm-8">
          <div class="btn-toolbar">
            <?= $templates->switchToggle('Export Playlist', 'doQuickCommand', '[\'musicplayer storing\']', '', (array_key_exists('song_storing', $botSettings) && $botSettings['song_storing'] == 1)) ?>
            <?= $templates->switchToggle('Exporting Playlist as ' . (array_key_exists('song_titles', $botSettings) && $botSettings['song_titles'] == '1' ? '.html' : '.txt'), 'doQuickCommand', '[\'musicplayer titles\']', '', (array_key_exists('song_titles', $botSettings) && $botSettings['song_titles'] == '1'), false, false, true) ?>
            <?= $templates->switchToggle('Whisper Mode', 'doQuickCommand', '[\'whispermode\']', '', (array_key_exists('whisper_mode', $botSettings) && $botSettings['whisper_mode'] == 'true')) ?>
          </div>
          <div class="spacer"></div>
          <div class="btn-toolbar">
            <?= $templates->switchToggle('Enable Event/Error Logging', $templates->_wrapInJsToggledDoQuickCommand('log', (array_key_exists('logenable', $botSettings) && $botSettings['logenable'] == '1' ? 'true' : 'false'), 'enable', 'disable'), '[]', '', (array_key_exists('logenable', $botSettings) && $botSettings['logenable'] == '1')) ?>
            <?= $templates->switchToggle('Enable Chat Logging', $templates->_wrapInJsToggledDoQuickCommand('logchat', (array_key_exists('logchat', $botSettings) && $botSettings['logchat'] == '1' ? 'true' : 'false'), 'enable', 'disable'), '[]', '', (array_key_exists('logchat', $botSettings) && $botSettings['logchat'] == '1')) ?>
          </div>
          <div class="spacer"></div>
          <div class="row">
            <div class="col-sm-4">
              <?= $templates->botCommandForm('log days', 'Set log rotate days', '[days]', (array_key_exists('logrotatedays', $botSettings) ? $botSettings['logrotatedays'] : '7'), 'Set') ?>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <?= $templates->informationPanel('The bot is able to whisper any messages intended for a specific user to that user, instead of posting it in the chat.') ?>
        </div>
      </div>
      <hr/>
      <h4>Connector Settings</h4>

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label>PhantomBot webserver address</label>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-ip" placeholder="<?= $config->botIp ?>"
                     value="<?= $config->botIp ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('botIp', 'setting-bot-ip', this)">Save</button>
              </span>
            </div>

            <p class="text-muted">
              This is generally the Ip address of the PC running PhantomBot.<br/>
              (Use "localhost" if you have PhantomBot running on the same PC as this webserver)
            </p>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>PhantomBot webserver base port</label>

            <div class="input-group">
              <input type="number" class="form-control" id="setting-bot-base-port"
                     placeholder="<?= $config->botBasePort ?>"
                     value="<?= $config->botBasePort ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('botBasePort', 'setting-bot-base-port', this)">
                  Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              This is by default "25000". Only change it if you have entered a custom port at the PhantomBot
              installation!
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Username for bot</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-name" placeholder="<?= $config->botName ?>"
                     value="<?= $config->botName ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('botName', 'setting-bot-name', this)">Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              The username of the account you used for PhantomBot.
            </p>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label>Bot account Oauth token</label>

            <div class="input-group">
              <input type="password" class="form-control" id="bot-oauth"
                     placeholder="oauth:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" value="<?= $config->botOauthToken ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('botOauthToken', 'setting-bot-oauth', this)">
                  Save
                </button>
              </span>
            </div>

            <p class="text-muted">
              This can be found in &quot;botlogin.txt&quot; in the installation folder of PhantomBot. (Use the &quot;oauth&quot;
              one)
            </p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Channel owner username</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-bot-owner" placeholder="<?= $config->channelOwner ?>"
                     value="<?= $config->channelOwner ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary" onclick="saveToConfig('channelOwner', 'setting-bot-owner', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <hr/>
      <h4>Bot Add-on Paths</h4>

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest follower file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-follower"
                     placeholder="<?= $config->paths['latestFollower'] ?>"
                     value="<?= $config->paths['latestFollower'] ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/latestFollower', 'setting-path-follower', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest donation file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-donation"
                     placeholder="<?= $config->paths['latestDonation'] ?>"
                     value="<?= $config->paths['latestDonation'] ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/latestDonation', 'setting-path-donation', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest current song file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-current-song"
                     placeholder="<?= $config->paths['youtubeCurrentSong'] ?>"
                     value="<?= $config->paths['youtubeCurrentSong'] ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/youtubeCurrentSong', 'setting-path-current-song', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <span>Latest song requests file</span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-playlist"
                     placeholder="<?= $config->paths['youtubePlaylist'] ?>"
                     value="<?= $config->paths['youtubePlaylist'] ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/youtubePlaylist', 'setting-path-playlist', this)">Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <span>Default playlist file <span class="text-muted(txt!)"></span></span>

            <div class="input-group">
              <input type="text" class="form-control" id="setting-path-default-playlist"
                     placeholder="<?= (array_key_exists('defaultYoutubePlaylist', $config->paths) ? $config->paths['defaultYoutubePlaylist'] : '') ?>"
                     value="<?= (array_key_exists('defaultYoutubePlaylist', $config->paths) ? $config->paths['defaultYoutubePlaylist'] : '') ?>"/>
              <span class="input-group-btn">
                <button class="btn btn-primary"
                        onclick="saveToConfig('paths/defaultYoutubePlaylist', 'setting-path-default-playlist', this)">
                  Save
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    $('#theme-selector').submit(function (event) {
      doBotRequest('saveToConfig', function () {
        //location.replace('/');
      }, {settingPath: 'paths/theme', setting: event.target[0].selectedOptions[0].value.trim()});
      event.preventDefault();
    });
  })();
</script>