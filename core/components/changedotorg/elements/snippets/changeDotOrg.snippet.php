<?php
/* 
 * changeDotOrg
 *
 * Returns changeDotOrg petition data. 
 *
 * 
 */

// Settings
$from = $modx->getOption('from',$scriptProperties,'signatures');
$get = $modx->getOption('get',$scriptProperties,'signature_count');
$tpl = $modx->getOption('tpl',$scriptProperties,'reasonsTpl');
$limit = $modx->getOption('limit',$scriptProperties,5);
$offset = $modx->getOption('offset',$scriptProperties,0);
$random = $modx->getOption('random',$scriptProperties,false);

/* Grab the class */
$path = $modx->getOption('core_path') . 'components/changedotorg/';
$path .= 'model/changedotorg/';
$cdo = $modx->getService('changedotorg','ChangeDotOrg', $path);

/* If we got the class (gotta be careful of failed migrations), grab settings and go! */
if ($cdo instanceof ChangeDotOrg) return 'got the class';

/* Set filters */
$where = array();
if ($key) $where['key'] = $key; 
if ($group) $where['group'] = $group;

/* Set cache id */
$cacheId = 'clientconfig';
if ($group) $cacheId = 'clientconfig.group.' . $group;
if ($key) $cacheId = 'clientconfig.key.' . $key;

/* Set cache key */
$contextKey = $modx->context->key;
$resourceCache = $modx->getOption('cache_resource_key', null, 'resource/' . $contextKey);



/* Format the output - upgraded to getChunk for output modifiers */
if (!$tpl) return print_r($settings);
foreach ($settings as $key => $value) {
     $output .= $modx->getChunk($tpl,array('key' => $key, 'value' => $value));
}

/* toPlaceholder support is handy too */
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,false);
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
    return '';
}

return $output;