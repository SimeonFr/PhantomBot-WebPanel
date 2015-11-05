<div class="youtube-icon icons">
  <span class="fa fa-youtube-play"></span>
</div>
<div id="controls" class="icons">
  <span class="fa fa-step-forward" role="button" onclick="doQuickCommand('skipsong')"></span>
</div>
<div class="icons">
  <?= $templates->switchToggle('<span id="music-player-shuffle" class="fa fa-random"></span>', 'doQuickCommand',
      '[\'musicplayer shuffle\']', '', (array_key_exists('song_shuffle', $botSettings) && $botSettings['song_shuffle'] == 1), true, true) ?>
</div>
<div class="icons">
  <span class="fa fa-volume-down" role="button" onclick="setMusicPlayerVolume(-10)"></span>

  <div id="music-player-volume">
    <div class="value v100"></div>
  </div>
  <span class="fa fa-volume-up" role="button" onclick="setMusicPlayerVolume(+10)"></span>
</div>
<div class="icons">
  <?= $templates->switchToggle('<span class="fa fa-volume-off"></span>&nbsp;/10', null, null, 'music-player-divide-volume', false, true, true) ?>
</div>
<div id="current-video-title">
  <div class="text">&nbsp;<?= $functions->cleanYTVideoTitle($musicPlayerCurrentSong) ?></div>
  <div class="text">&nbsp;<?= $functions->cleanYTVideoTitle($musicPlayerCurrentSong) ?></div>
</div>
<div class="options">
  <button class="btn btn-default btn-sm dropdown-toggle">Options</button>
  <ul class="dropdown-menu drop-up" role="menu">
    <li><?= $templates->botCommandForm('musicplayer limit', 'Limit per viewer', 'amount', (array_key_exists('song_limit', $botSettings) ? $botSettings['song_limit'] : ''), 'Limit', false, true, true) ?></li>
    <li><?= $templates->botCommandForm('pricecom addsong', '!addsong price', 'amount', $functions->getIniValueByKey('pricecom', 'addsong'), 'Set', false, true, true) ?></li>
    <li><?= $templates->botCommandForm('addsong', 'Add song', 'link', null, 'Add', false, true, true) ?></li>
    <li><?= $templates->botCommandForm('delsong', 'Remove song', 'link', null, 'Del', false, true, true) ?></li>
    <li><?= $templates->botCommandForm('playsong', 'Play song form playlist', '#id', null, 'Play', false, true, true) ?></li>
    <li>
      <div class="btn-toolbar">
        <?= $templates->switchToggle('Toggle Messages', 'doQuickCommand(\'musicplayer toggle\')', '[]', '',
            (array_key_exists('song_toggle', $botSettings) && $botSettings['song_toggle'] == '1'), true) ?>
        <?= $templates->botCommandButton('stealsong', 'Steal Song', 'default btn-sm') ?>
        <?= $templates->botCommandButton('reloadplaylist', 'Reload "playlist.txt"', 'default btn-sm') ?>
      </div>
    </li>
  </ul>
</div>
<div class="options">
  <button class="btn btn-default btn-sm dropdown-toggle">Playlist</button>
  <ul id="music-player-requests" class="dropdown-menu drop-up" role="menu">
    <li>Waiting for load...</li>
  </ul>
</div>
<div class="options">
  <a href="/music-player.php?botControl=true" target="_blank">
    <button class="btn btn-primary btn-sm">Open Player</button>
  </a>
</div>