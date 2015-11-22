<?php
/**
 * chat-moderator.php
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
$urlWhiteList = $functions->getIniArray('whitelist');

?>
<div class="app-part">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        Chat Moderator
        <?= $templates->toggleFavoriteButton() ?>
        <?= $templates->moduleActiveIndicator($functions->getModuleStatus('chatModerator.js')) ?>
      </h3>
    </div>
    <div class="panel-body">
      <h4>Moderate Now</h4>

      <div class="row">
        <div class="col-sm-4">
          <?= $templates->botCommandForm('permit', 'Permit viewer to post a link') ?>
          <?= $templates->botCommandForm('purge', 'Purge viewer') ?>
          <?= $templates->botCommandForm('timeout', 'Time-Out viewer') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('ban', 'Ban viewer') ?>
          <?= $templates->botCommandForm('unban', 'Unban viewer') ?>
        </div>
        <div class="col-sm-4">
          <?= $templates->botCommandForm('autopurge', 'Purge on phrase', 'phrase') ?>
          <?= $templates->botCommandForm('autoban', 'Ban on phrase', 'phrase') ?>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Warning Types &amp; Messages</h4>

      <div class="collapsible-content">
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning1type', 'Warning #1 type', 'purge/ban/timeout',
                (array_key_exists('warning1type', $botSettings) ? $botSettings['warning1type'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning2type', 'Warning #2 type', 'purge/ban/timeout',
                (array_key_exists('warning2type', $botSettings) ? $botSettings['warning2type'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning3type', 'Warning #3 type', 'purge/ban/timeout',
                (array_key_exists('warning3type', $botSettings) ? $botSettings['warning3type'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning1message', 'Warning #1 message', 'message',
                (array_key_exists('warning1message', $botSettings) ? $botSettings['warning1message'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning2message', 'Warning #2 message', 'message',
                (array_key_exists('warning2message', $botSettings) ? $botSettings['warning2message'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod warning3message', 'Warning #3 message', 'message',
                (array_key_exists('warning3message', $botSettings) ? $botSettings['warning3message'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Links</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'Allow Links',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod linksallowed',
                  (array_key_exists('linksallowed', $botSettings) && $botSettings['linksallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('linksallowed', $botSettings) && $botSettings['linksallowed'] == 1)
          ); ?>
          <?= $templates->switchToggle(
              'Allow Youtube Links',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod youtubeallowed',
                  (array_key_exists('youtubeallowed', $botSettings) && $botSettings['youtubeallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('youtubeallowed', $botSettings) && $botSettings['youtubeallowed'] == 1)
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod linksmessage', 'No link permission message', 'message',
                (array_key_exists('linksmessage', $botSettings) ? $botSettings['linksmessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('whitelist', 'Whitelist Url', 'Url', (array_key_exists('link', $urlWhiteList) ? $urlWhiteList['link'] : ''), 'Add') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Caps</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'Moderate Caps',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod capsallowed',
                  (array_key_exists('capsallowed', $botSettings) && $botSettings['capsallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('capsallowed', $botSettings) && $botSettings['capsallowed'] == '0')
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod capsmessage', 'Too many caps message', 'message',
                (array_key_exists('capsmessage', $botSettings) ? $botSettings['capsmessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod capstriggerratio', 'Max caps ratio', 'ratio',
                (array_key_exists('capstriggerratio', $botSettings) ? $botSettings['capstriggerratio'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod capstriggerlength', 'Max caps length', 'amount',
                (array_key_exists('capstriggerlength', $botSettings) ? $botSettings['capstriggerlength'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Spam</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'moderate Spam',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod spamallowed',
                  (array_key_exists('spamallowed', $botSettings) && $botSettings['spamallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('spamallowed', $botSettings) && $botSettings['spamallowed'] == '0')
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod spammessage', 'Spam message', 'message',
                (array_key_exists('spammessage', $botSettings) ? $botSettings['spammessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod spamlimit', 'Spam limit', 'amount',
                (array_key_exists('spamlimit', $botSettings) ? $botSettings['spamlimit'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Symbols</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'Moderate Symbols',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod symbolsallowed',
                  (array_key_exists('symbolsallowed', $botSettings) && $botSettings['symbolsallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('symbolsallowed', $botSettings) && $botSettings['symbolsallowed'] == '0')
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod symbolsmessage', 'Too many symbols message', 'message',
                (array_key_exists('symbolsmessage', $botSettings) ? $botSettings['symbolsmessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod symbolslimit', 'Max symbols limit', 'amount',
                (array_key_exists('symbolslimit', $botSettings) ? $botSettings['symbolslimit'] : ''), 'Set') ?>
          </div>
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod symbolsrepeatlimit', 'Max symbols repeat limit', 'amount',
                (array_key_exists('symbolsrepeatlimit', $botSettings) ? $botSettings['symbolsrepeatlimit'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Word Repeat</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'Moderate Repeating Characters',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod repeatallowed',
                  (array_key_exists('repeatallowed', $botSettings) && $botSettings['repeatallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('repeatallowed', $botSettings) && $botSettings['repeatallowed'] == '0')
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod repeatmessage', 'Word repeat message', 'message',
                (array_key_exists('repeatmessage', $botSettings) ? $botSettings['repeatmessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod repeatlimit', 'Word repeat limit', 'amount',
                (array_key_exists('repeatlimit', $botSettings) ? $botSettings['repeatlimit'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
      <hr/>
      <h4 class="collapsible-master">Graphemes</h4>

      <div class="collapsible-content">
        <div class="btn-toolbar">
          <?= $templates->switchToggle(
              'Moderate Graphemes',
              $templates->_wrapInJsToggledDoQuickCommand(
                  'chatmod graphemeallowed',
                  (array_key_exists('graphemeallowed', $botSettings) && $botSettings['graphemeallowed'] == '0' ? 'true' : 'false'),
                  'true',
                  'false'
              ),
              null,
              null,
              (array_key_exists('graphemeallowed', $botSettings) && $botSettings['graphemeallowed'] == '0')
          ); ?>
        </div>
        <div class="row">
          <div class="col-sm-8">
            <?= $templates->botCommandForm('chatmod graphememessage', 'Too many graphemes message', 'message',
                (array_key_exists('graphememessage', $botSettings) ? $botSettings['graphememessage'] : ''), 'Set') ?>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <?= $templates->botCommandForm('chatmod graphemelimit', 'Grapheme limit', 'amount',
                (array_key_exists('graphemelimit', $botSettings) ? $botSettings['graphemelimit'] : ''), 'Set') ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>