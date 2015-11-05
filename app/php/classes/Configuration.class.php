<?php
/**
 * Configuration.class.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:45
 */
class Configuration
{
  public $botIp;
  public $botBasePort;
  public $botName;
  public $channelOwner;
  public $botOauthToken;
  public $panelUsers;
  public $paths;

  public $version;
  public $pBCompat;

  private $configFileName;

  public function Configuration($isInstall = false)
  {
    $this->configFileName = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/content/vars/config.php';
    if (!$isInstall) {
      if (!file_exists($this->configFileName)) {
        header('Location: http://' . filter_input(INPUT_SERVER, 'SERVER_NAME') . '/app/php/install.php');
      }
      $configFileContent = @file_get_contents($this->configFileName);
      if ($configFileContent) {
        $configFileContent = json_decode($configFileContent, true);
        foreach ($configFileContent as $key => $value) {
          if ($value == '') {
            echo 'Please open the configuration file at "' . realpath($this->configFileName) . '" and fill in the settings.';
            exit;
          } else {
            $this->$key = $value;
          }
        }
      } else {
        echo 'Could not read from config file! It is supposed to be located at "' . realpath($this->configFileName)
            . '". Check if it\'s there and if your webserver has the appropriate rights to read the file.';
        exit;
      }
    }

    // App version
    $this->version = @file_get_contents(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/app/content/vars/current_version.txt');
    // Compatible PhantomBot version
    $this->pBCompat = '1.6.4-stable';
  }

  /**
   * @param string $settingPath
   * @param string $setting
   * @return bool
   */
  public function saveToConfigWeb($settingPath, $setting)
  {
    $settingPath = explode('/', $settingPath);
    $settingArray = [];

    switch (count($settingPath)) {
      case 1:
        $settingArray[$settingPath[0]] = $setting;
        break;
      case 2:
        $path1Copy = $this->$settingPath[0];
        $path1Copy[$settingPath[1]] = $setting;
        $settingArray[$settingPath[0]] = $path1Copy;
        break;
      default:
        return false;
        break;
    }

    return $this->_saveToConfig($settingArray);
  }

  /**
   * @param array $data
   * @return bool
   */
  public function _saveToConfig($data){
    if (!file_exists($this->configFileName)) {
      if (is_writeable(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'))) {
        $data['CONFIG HELP'] = [
          'Slashes' => 'Slashes in this file NEED to be escaped',
        ];
        $fileSaveSuccess = @file_put_contents($this->configFileName, json_encode($data, JSON_PRETTY_PRINT));
        if ($fileSaveSuccess) {
          return chmod($this->configFileName, 0744);
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      $existingFile = @file_get_contents($this->configFileName);
      if ($existingFile) {
        $existingFile = json_decode($existingFile, true);
        foreach ($data as $key => $value) {
          $existingFile[$key] = $value;
        }
        return (@file_put_contents($this->configFileName, json_encode($existingFile, JSON_PRETTY_PRINT)));
      } else {
        return false;
      }
    }
  }
}