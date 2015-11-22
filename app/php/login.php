<?php
/**
 * login.php
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:46
 */
define('BASEPATH', realpath(dirname(__FILE__)) . '/../..');

require_once(BASEPATH . '/app/php/classes/Configuration.class.php');
require_once(BASEPATH . '/app/php/classes/ConnectionHandler.class.php');
require_once(BASEPATH . '/app/php/classes/Functions.class.php');

$config = new Configuration();
$connection = new ConnectionHandler($config);
$functions = new Functions($config, $connection);
$input = filter_input_array(INPUT_POST);

if (!array_key_exists('username', $input) || trim($input['username']) == '') {
  $functions->sendBackError('No username defined', 203);
} elseif (!array_key_exists('password', $input) || trim($input['password']) == '') {
  $functions->sendBackError('No password defined', 203);
} elseif (!$functions->isValidUser($input['username'], $input['password'])) {
  $functions->sendBackError('No Access', 203);
} else {
  $functions->sendBackOk(true);
}