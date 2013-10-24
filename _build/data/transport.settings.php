<?php

$s = array(
    'changedotorg_api_key' => '',
    'changedotorg_api_secret' => '',
    'changedotorg_cache_expires' => '7200',
    'changedotorg_cache_key' => 'changedotorg',
    'changedotorg_petition_id' => '',
    'changedotorg_petition_url' => '',
);

$settings = array();

foreach ($s as $key => $value) {
    if (is_string($value) || is_int($value)) { $type = 'textfield'; }
    elseif (is_bool($value)) { $type = 'combo-boolean'; }
    else { $type = 'textfield'; }

    $name = str_replace('_',' ',strtoupper($key));

    if ($key === 'changedotorg_api_key') $desc = 'Required to access change.org petition data at all.';
    elseif ($key === 'changedotorg_petition_url') $desc = 'Required to get petition data. Can also be set per snippet call using the &petitionUrl property.';
    else $desc = '';

    $settings[$key] = $modx->newObject('modSystemSetting');
    $settings[$key]->set('key', $key);
    $settings[$key]->fromArray(array(
        'value' => $value,
        'xtype' => $type,
        'namespace' => 'changedotorg',
        'name' => 'Change.org' . $name,
        'description' => $desc,
    ));
}

return $settings;


