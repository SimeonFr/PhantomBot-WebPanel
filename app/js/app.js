/**
 * app.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:33
 */
var pBotStorage = {
      keys: {
        twitchCache: 'pbot-twitch-cache',
        chatDefaultState: 'pbot-chat-state',
        panelLogin: 'pbot-login',
        lastUsedPart: 'pbot-last-used-part',
        favoritesMenuItems: 'pbot-favorites-menu-items',
        informationActive: 'pbot-information-active',
        tooltipsActive: 'pbot-tooltips-active',
        musicPlayer: {
          volume: 'pbot-music-player-volume',
          controlsEnabled: 'pbot-music-player-controls',
          volumeDivisionEnabled: 'pbot-music-player-volume-division',
          lastVolumeUpdate: 'pbot-music-player-last-update',
        },
      },
      set: function (settingName, data) {
        localStorage.setItem(settingName, JSON.stringify(data));
      },
      get: function (settingName, returnValue) {
        return (localStorage.getItem(settingName) ? JSON.parse(localStorage.getItem(settingName)) : (returnValue ? returnValue : false));
      },
    },
    pBotData = {
      config: {
        owner: '',
        botName: '',
        isBotOnline: false,
        token: '',
      },
      login: null,
      touchedCollapsibles: [],
      streamPreviewActive: false,
      informationActive: pBotStorage.get(pBotStorage.keys.informationActive, true),
      tooltipsActive: pBotStorage.get(pBotStorage.keys.tooltipsActive, true),
      musicPlayerControls: {
        controlsEnabled: pBotStorage.get(pBotStorage.keys.musicPlayer.controlsEnabled),
        volume: pBotStorage.get(pBotStorage.keys.musicPlayer.volume, 100),
        volumeDivision: pBotStorage.get(pBotStorage.keys.musicPlayer.volumeDivisionEnabled),
        updateIntervalId: -1,
      },
    };

$(window).ready(function () {
  /* Retrieve ?login data from local storage */
  pBotData.login = pBotStorage.get(pBotStorage.keys.panelLogin);

  if (pBotData.login) {
    /* Load panel config */
    getPanelConfig();

    /* Get bot status */
    getBotStatus();

    /* Bind click event to dropdown-toggles to toggle their respective lists */
    bindGlobalEventHandlers();

    /* Load up the favorites menu */
    updateFavoritesMenu();

    /* Set interval to update channel data every 30 minutes */
    setInterval(function () {
      loadChannelData();
      getBotStatus();
    }, 6e5);
  } else {
    logOut();
  }
});

function addSecondChat() {
  var input = $('#second-chat-form-input');
  if (input.val() != '') {
    $('#chat-sidebar')
        .addClass('double-chat')
        .append('<iframe id="chat-iframe-secondary" src="http://www.twitch.tv/' + input.val().toLocaleLowerCase() + '/chat?popout=" scrolling="no"></iframe>');
  }
}

function applyChannelData(channelData) {
  if (channelData.stream != null) {
    $('#stream-status').text('Online');
    //noinspection JSUnresolvedFunction
    if (!channelData.stream.hasOwnProperty('viewers')) {
      channelData.stream.viewers = 0;
    }
    $('#stream-viewer-count').text(channelData.stream.viewers);
  }
  $('#stream-title').text(channelData.channel.status);
  //noinspection JSUnresolvedFunction
  if (!channelData.channel.hasOwnProperty('game')) {
    channelData.channel.game = '';
  }
  $('#stream-game').text(channelData.channel.game);
  //noinspection JSUnresolvedFunction
  if (!channelData.followers.hasOwnProperty('_total')) {
    channelData.followers._total = 0;
  }
  $('#stream-followers').text(channelData.followers._total);

  doBotRequest('getIniValueByKey', function (result) {
    if (result != '0') {
      $('#stream-hosts').text(data[0]);
    }
  }, {uri: 'stream_info', key: 'hosts_amount'});
}

function bindGlobalEventHandlers() {
  $('.dropdown-toggle').on('click', function () {
    var owner = $(this),
        targetList = owner.next('.dropdown-menu'),
        timer,
        fadeoutTime = 300;
    if (targetList.css('display') == 'block') {
      targetList.fadeOut(fadeoutTime);
    } else {
      $('.dropdown-menu').fadeOut(fadeoutTime);
      if (targetList.hasClass('drop-up')) {
        targetList.fadeIn(fadeoutTime);
      } else {
        targetList
            .fadeIn(fadeoutTime)
            .on('click mouseleave', function () {
              timer = setTimeout(function () {
                targetList
                    .fadeOut(fadeoutTime)
                    .off('click mouseleave mouseenter');
              }, 300);
            })
            .on('mouseenter', function () {
              clearTimeout(timer);
            });
      }
    }
  });
  $('#volume-control-slider').slider({
    min: 0,
    max: 100,
    value: pBotData.musicPlayerControls.volume,
    change: function (event, ui) {
      doQuickCommand('volume ' + ui.value);
    }
  });
}

function bindPartEventHandlers() {
  $('form.bot-command-form').off('submit').on('submit', function (event) {
    var requestParams;
    if (!event.target.attributes.hasOwnProperty('botcommand')) {
      event.target.attributes.botcommand = {value: ''};
    }
    //noinspection JSUnresolvedVariable,CoffeeScriptUnusedLocalSymbols
    var input = $(event.target[0]), noReload = event.target.attributes.formnoreload.value;
    if (input.val().length != 0) {
      if (event.target.attributes.botcommand.value == '') {
        requestParams = {command: _cleanInput(input.val())};
      } else {
        requestParams = {command: _cleanInput(event.target.attributes.botcommand.value) + ' ' + _cleanInput(input.val())};
      }
      doBotRequest('command', function (result) {
        showGeneralAlert(result, 'success');
        if (noReload == '1') {
          input.val('');
        } else {
          loadPartFromStorage();
        }
      }, requestParams);
    } else {
      showGeneralAlert();
    }
    event.preventDefault();
  });

  $('form.combined-bot-command-form').off('submit').on('submit', function (event) {
    //noinspection JSUnresolvedVariable
    var command = event.target.attributes.botcommand.value,
        action = event.target[0].selectedOptions[0].value,
        actionArg = event.target[1].value,
        noReload = (event.target.attributes.formnoreload.value == '1'),
        requestParams;

    if (actionArg != '') {
      requestParams = {command: (command != '' ? command + ' ' : '') + action + ' ' + actionArg};
      doBotRequest('command', function (result) {
        showGeneralAlert(result, 'success');
        if (noReload) {
          $(event.target[1]).val('');
        } else {
          loadPartFromStorage();
        }
      }, requestParams);
    } else {
      showGeneralAlert();
    }



    debug(command, action, actionArg);
    event.preventDefault();
  });

  $('.collapsible-master').on('click', function () {
    var owner = $(this),
        child = owner.next('.collapsible-content');
    if (owner.hasClass('open')) {
      child.fadeOut(300);
      for (var i in pBotData.touchedCollapsibles) {
        //noinspection JSUnfilteredForInLoop
        if (pBotData.touchedCollapsibles[i] == owner.text()) {
          //noinspection JSUnfilteredForInLoop
          pBotData.touchedCollapsibles.splice(i, 1);
        }
      }
    } else {
      child.fadeIn(300);
      pBotData.touchedCollapsibles.push(owner.text());
    }
    owner.toggleClass('open');
  });

  /*
   * Open previously opened collapsibles for current part
   * pBotData.touchedCollapsibles gets reset on changing parts
   */
  for (var i in pBotData.touchedCollapsibles) {
    //noinspection JSUnfilteredForInLoop
    $('.collapsible-master:contains(' + pBotData.touchedCollapsibles[i] + ')')
        .toggleClass('open')
        .next('.collapsible-content').fadeIn(300);
  }

  toggleTooltips(true);
  toggleInformationPanels(true);
}

function calculateStreamHeight(event) {
  var that;
  if (event) {
    that = $(event.currentTarget);
  } else {
    that = $('#stream-preview');
  }
  that.height(that.outerWidth() * 0.5625 + 30);
}

function channelDataCache(data) {
  if (data) {
    data.pbotCacheEndTime = Date.now() + 3e5;
    pBotStorage.set(pBotStorage.keys.twitchCache, data);
  } else {
    return pBotStorage.get(pBotStorage.keys.twitchCache, false);
  }
}

function clearSecondChat() {
  $('#chat-iframe-secondary').remove();
  $('#chat-sidebar').removeClass('double-chat');
  $('#second-chat-form-input').val('');
}

function doBotRequest(action, callback, params) {
  if (!pBotData.config.isBotOnline) {
    return;
  }
  $.ajax({
    type: 'POST',
    url: 'app/php/connect.php',
    data: $.extend({action: action}, params, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[1] == 200) {
        if (callback) {
          callback.apply(this, [data[0], data[1]]);
        }
      } else {
        showGeneralAlert('Bot Request failed!');
        console.log('Bot Request failed...', data);
      }
    },
  });
}

function doQuickCommand(command, confirmAction, noReload) {
  if (confirmAction && confirmAction != '') {
    if (!confirm(confirmAction)) {
      return;
    }
  }
  doBotRequest('command', function (result) {
    showGeneralAlert(result, 'success');
    if (!noReload) {
      loadPartFromStorage();
    }
  }, {command: _cleanInput(command)});
}

function getPanelConfig() {
  $.ajax({
    type: "POST",
    url: '/app/php/connect.php',
    data: $.extend({action: 'getConfig'}, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[1] == 200) {
        pBotData.config = data[0];
        loadChannelData();
        /* Apply UI Settings */
        toggleMusicPlayerControls(true, $('#player-controls-toggle'));
        toggleTooltips(true);
        toggleChat(true, $('#toggle-chat'));
        loadPartFromStorage();

      } else {
        showLoginAlert(data[3])
      }
    },
  });
}

function loadChannelData(skipCache) {
  var cache = channelDataCache();
  if (!skipCache && cache && cache.pbotCacheEndTime > Date.now()) {
    console.log('Stream info loaded from local cache!', cache);
    applyChannelData(cache);
  } else {
    var newCache = {
      stream: null,
      channel: null,
      followers: null
    };
    $.ajax({
      url: 'https://api.twitch.tv/kraken/channels/' + pBotData.config.owner + '/follows?limit=1',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        newCache.followers = data;
        $.ajax({
          url: 'https://api.twitch.tv/kraken/streams/' + pBotData.config.owner,
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            if (data.stream != null) {
              newCache.stream = data.stream;
              newCache.channel = data.stream.channel;
              channelDataCache(newCache);
              applyChannelData(newCache);
            } else {
              $.ajax({
                url: 'https://api.twitch.tv/kraken/channels/' + pBotData.config.owner,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                  newCache.channel = data;
                  channelDataCache(newCache);
                  applyChannelData(newCache);
                }
              });
            }
          }
        });
      }
    });
  }
}

function loadPartFromStorage() {
  var partUrl = pBotStorage.get(pBotStorage.keys.lastUsedPart, 'static/dashboard.php');
  setActiveMenuItem(partUrl);
  $('#part-window').load('app/parts/' + partUrl, {token: pBotData.config.token}, bindPartEventHandlers);
}

//noinspection JSUnusedGlobalSymbols
function logOut() {
  localStorage.removeItem(pBotStorage.keys.twitchCache);
  localStorage.removeItem(pBotStorage.keys.panelLogin);
  location.replace('/');
}

//noinspection JSUnusedGlobalSymbols
function openPart(partUrl, filters) {
  pBotData.touchedCollapsibles = [];
  setActiveMenuItem(partUrl);
  if (filters) {
    filters = $.extend(filters, {token: pBotData.config.token});
    $('#part-window').load('app/parts/' + partUrl, filters, bindPartEventHandlers);
  } else {
    $('#part-window').load('app/parts/' + partUrl, {token: pBotData.config.token}, bindPartEventHandlers);
  }
  pBotStorage.set(pBotStorage.keys.lastUsedPart, partUrl);
}

function saveToConfig(settingPath, inputId, button) {
  var setting = $('#' + inputId).val();

  doBotRequest('saveToConfig', function (status) {
    console.log(status);
    $(button)
        .removeClass('btn-primary')
        .addClass('btn-success')
        .text('Saved')
        .prop('disabled', true);
    var timer = setTimeout(function () {
      $(button)
          .removeClass('btn-success')
          .addClass('btn-primary')
          .text('Save')
          .prop('disabled', false);
      clearTimeout(timer);
    }, 3e3)
  }, {settingPath: settingPath, setting: setting.trim()});
}

function setActiveMenuItem(partUrl) {
  $('.active').removeClass('active');
  $('#menu-parent-' + partUrl.replace('static/', '').replace(/([a-z]+).*/i, '$1')).addClass('active');
  $('#menu-favorites-' + partUrl.replace(/.*\/|-|\.php/ig, '')).addClass('active');
}

function getBotStatus() {
  $.ajax({
    type: 'POST',
    url: 'app/php/connect.php',
    data: $.extend({action: 'testBotConnection'}, pBotData.login),
    dataType: 'json',
    success: function (data) {
      if (data[2] == 52) {
        pBotData.config.isBotOnline = true;
      } else {
        console.warn('Bot is offline!\nDisabling bot requests...\nChecking again in 10 minutes...\n' + data[3]);
      }
    },
  });
}

function showGeneralAlert(message, severity) {
  if (!message) {
    message = 'You forgot to enter a value!';
  }
  if (!severity) {
    severity = 'danger';
  }
  $('#general-alert')
      .addClass('alert-' + severity)
      .text(message)
      .fadeIn(300);
  var t = setTimeout(function () {
    $('#general-alert').fadeOut(700);
    clearTimeout(t);
  }, 2e3);
}

function toggleInformationPanels(fromPageLoad) {
  var panels = $('.information-panel');
  if (fromPageLoad) {
    panels.toggleClass('hidden', !pBotData.informationActive);
  } else {
    pBotData.informationActive = !pBotData.informationActive;
    panels.toggleClass('hidden', pBotData.informationActive);
    pBotStorage.set(pBotStorage.keys.informationActive, pBotData.informationActive);
    loadPartFromStorage();
  }
}

//noinspection CoffeeScriptUnusedLocalSymbols
function toggleMusicPlayerControls(fromLoad, button) {
  if (fromLoad) {
    $('#music-player-divide-volume').prop({checked: pBotData.musicPlayerControls.volumeDivision});
  } else {
    pBotData.musicPlayerControls.controlsEnabled = !pBotData.musicPlayerControls.controlsEnabled;
    pBotStorage.set(pBotStorage.keys.musicPlayer.controlsEnabled, pBotData.musicPlayerControls.controlsEnabled);
  }
  var body = $('body'),
      enabledClassName = 'music-controls-enabled';

  if (!pBotData.musicPlayerControls.controlsEnabled) {
    if (pBotData.musicPlayerControls.updateIntervalId != -1) {
      clearInterval(pBotData.musicPlayerControls.updateIntervalId);
    }
    body.removeClass(enabledClassName);
    $(button).html('<span class="fa fa-eject"></span>&nbsp;Show Music Player Controls');
  } else {
    body.addClass(enabledClassName);
    pBotData.musicPlayerControls.updateIntervalId = setInterval(updateMusicPlayerState, 5e3);
    $(button).html('<span class="fa fa-eject text-success"></span>&nbsp;Hide Music Player Controls');
  }
}

function toggleStreamPreview(fromLoad) {
  if (fromLoad != undefined) {
    pBotData.streamPreviewActive = false;
    return;
  }
  var streamPreview = $('#stream-preview');
  if (pBotData.streamPreviewActive) {
    pBotData.streamPreviewActive = false;
    streamPreview
        .removeClass('open')
        .attr('src', '');
  } else {
    pBotData.streamPreviewActive = true;
    streamPreview
        .addClass('open')
        .attr('src', 'http://twitch.tv/' + pBotData.config.owner + '/embed');
  }
  $('#stream-preview-fit-video').toggle(pBotData.streamPreviewActive);
}

function toggleTooltips(fromLoad) {
  if (fromLoad) {
    bindTooltips(pBotData.tooltipsActive);
  } else {
    bindTooltips(pBotData.tooltipsActive);
    pBotData.tooltipsActive = !pBotData.tooltipsActive;
    pBotStorage.set(pBotStorage.keys.tooltipsActive, pBotData.tooltipsActive);
    loadPartFromStorage();
  }
}

//noinspection JSUnusedGlobalSymbols,CoffeeScriptUnusedLocalSymbols
function toggleChat(fromLoad, button) {
  //noinspection CoffeeScriptUnusedLocalSymbols
  var body = $('body'),
      chatIframe = $('#chat-iframe'),
      enabledClassName = 'chat-enabled',
      chatEnabled = body.hasClass(enabledClassName);
  if (fromLoad) {
    if (pBotStorage.get(pBotStorage.keys.chatDefaultState)) {
      body.addClass(enabledClassName);
      chatIframe.attr('src', 'http://www.twitch.tv/' + pBotData.config.owner + '/chat?popout=');
      $(button).html('<span class="fa fa-eject text-success"></span>&nbsp;Hide Chat');
    }
    return;
  }
  if (chatEnabled) {
    body.removeClass(enabledClassName);
    chatIframe.attr('src', '');
    $(button).html('<span class="fa fa-eject"></span>&nbsp;Show Chat')
  } else {
    body.addClass(enabledClassName);
    chatIframe.attr('src', 'http://www.twitch.tv/' + pBotData.config.owner + '/chat?popout=');
    $(button).html('<span class="fa fa-eject text-success"></span>&nbsp;Hide Chat')
  }
}

//noinspection JSUnusedGlobalSymbols
function toggleChatDefaultState() {
  var currentSetting = !pBotStorage.get(pBotStorage.keys.chatDefaultState);
  pBotStorage.set(pBotStorage.keys.chatDefaultState, currentSetting);
}

function updateFavoritesMenu(itemName, itemPath) {
  var favorites = pBotStorage.get(pBotStorage.keys.favoritesMenuItems, []);
  if (itemName && itemPath) {
    var exists;
    for (var i in favorites) {
      //noinspection JSUnfilteredForInLoop
      if (favorites[i].itemName == itemName) {
        exists = true;
        //noinspection JSUnfilteredForInLoop
        favorites.splice(i, 1);
      }
    }
    if (!exists) {
      favorites.push({
        itemName: itemName,
        itemPath: itemPath
      });
    }
    pBotStorage.set(pBotStorage.keys.favoritesMenuItems, favorites);
    setTimeout(updateFavoritesMenu, 10);
  } else {
    var favoritesMenu = $('#favorites-menu');
    favoritesMenu.children('.favorites-item').each(function (i, e) {
      $(e).remove();
    });
    for (i in favorites) {
      //noinspection JSUnfilteredForInLoop
      favoritesMenu.append($('<li class="favorites-item" id="menu-favorites-' + favorites[i].itemName.replace(' ', '').toLocaleLowerCase()
          + '"><a nohref onclick="openPart(\'' + favorites[i].itemPath + '\')" role="button">' + favorites[i].itemName + '</a></li>'));
    }
  }
}

function updateMusicPlayerState() {
  if (pBotData.musicPlayerControls.controlsEnabled) {
    doBotRequest('getCurrentTitle', function (result) {
      var titleElement = $('#current-video-title'),
          titleText = result;
      if ((titleText.length * 9) > titleElement.width()) {
        titleElement.find('.text').html('&nbsp;' + titleText + _repeat('&nbsp;', 10));
        titleElement.addClass('long');
      } else {
        titleElement.find('.text:first-child').html('&nbsp;' + titleText);
        titleElement.removeClass('long');
      }
    });
  }
}

function togglePlayPause() {
  doBotRequest('command', function (result) {
    showGeneralAlert(result, 'success');
  }, {command: 'pause'});
}

function _repeat(string, count) {
  var str = '', i = 0;
  for (i; i < count; i++) {
    str += string;
  }
  return str;
}

function _cleanInput(input) {
  var search = [/^!/],
      replacements = [''],
      i = 0;
  for (i; i < search.length - 1; i++) {
    input.replace(search[i], replacements[i])
  }
  return input;
}

//noinspection JSUnusedGlobalSymbols
function debug() {
  console.log.apply(console, arguments);
}
