<?php
/* 
 * changeDotOrg
 *
 * Returns changeDotOrg petition data. 
 *
 * [[changeDotOrg]]
 * returns: signature count.
 *
 * [[changeDotOrg?from=`reasons`]]
 * returns: 
 * 
 */

// First off...
$apiKey = $modx->getOption('changedotorg_api_key');
if(empty($apiKey)) return 'API key is required in system settings.';

// Settings
$from = $modx->getOption('from',$scriptProperties,'signatures');
$default = ($from === 'signatures') ? 'signature_count' : $from;
$get = $modx->getOption('get',$scriptProperties,$default);
$tpl = $modx->getOption('tpl',$scriptProperties,'changeDotOrg-' . $from . 'Tpl');
$limit = $modx->getOption('limit',$scriptProperties,5);
$offset = $modx->getOption('offset',$scriptProperties,0);
$random = $modx->getOption('random',$scriptProperties,false);
$expires = $modx->getOption('changedotorg_cache_expires');

// Grab the class
$path = $modx->getOption('core_path') . 'components/changedotorg/';
$path .= 'model/changedotorg/';
$cdo = $modx->getService('changedotorg','ChangeDotOrg', $path);

/* If we got the class (gotta be careful of failed migrations), we grab the data. */
if ($cdo instanceof ChangeDotOrg) {

  // We're gonna need the Petition ID 
  $petitionId = $modx->getOption('changedotorg_petition_id');
  if(!petitionId || empty($petitionId)) {
    $url = $modx->getOption('changedotorg_petition_url');
    if(empty($url)) return 'Petition URL is required in system settings to get the Petition ID';
    $petitionId = $cdo->getPetitionId($url,$apiKey);
  }

  // Get Petition Data
  $requestedData = $cdo->getPetitionData($petitionId,$apiKey,$from,$expires);
  
} else {
  return 'Failed to get required ChangeDotOrg class.';
}

// Return stuff
if (!$requestedData) return 'Failed to get data.';
if ($from === 'targets') { $results = $requestedData; }
else { $results = $requestedData[$get]; }

$i=0;
$c=0;
if (is_array($results)) {
    if ($random) {
        $limit = 1;
        $offset = rand(0,count($results));
    }
    foreach($results as $item) {
        if ($offset && $c<$offset) { $c++; continue; }
        if ($i>=$limit) break;
        $output .= $modx->getChunk($tpl,$item);
        $i++;
    }
    return $output;
}
return $results;