<?php
$min = filter_input(INPUT_POST, 'min');
$ini = filter_input(INPUT_POST, 'ini');
$cleanIni = '';

if ($min && $ini) {
  $tempIni = @parse_ini_string($ini, false, INI_SCANNER_RAW);
  $tempIni = array_filter($tempIni, function ($value) use ($min) {
    return (intval($value) >= intval($min));
  });
  $cleanIni = buildIni($tempIni);
}

function buildIni($iniArray) {
  $string = '';
  foreach ($iniArray as $key=>$value) {
    $string .= $key . '=' . $value . "\n";
  }
  return $string;
}

?>
<html>
<head>
  <title>Clean PhantomBot Ini File</title>
  <style>
    textarea {
      width: 300px;
      height: 300px;
    }

    label, textarea, button {
      display: block;
      clear: both;
    }
  </style>
</head>
<body>
<p>
  Clean ini files to contain only values from specified.
</p>

<form action="/app/php/cleanIni.php" method="post">
  <label for="min-input">
    Minimal Value:
  </label>
  <input type="text" name="min" id="min-input" value="10" />
  <label for="old-ini">
    Ini to clean:
  </label>
  <textarea name="ini" id="old-ini"><?= $ini ?></textarea>
  <button type="submit">Clean</button>
</form>
<p>&nbsp;</p>
<label for="clean-ini">
  Output
</label>
<textarea id="clean-ini"><?= $cleanIni; ?></textarea>
</body>
</html>