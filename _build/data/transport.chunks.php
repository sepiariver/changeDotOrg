<?php
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 1,
    'name' => 'changeDotOrg-reasonsTpl',
    'description' => 'The tpl chunk for snippet calls to get reasons data. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'reasons.chunk.tpl'),
));

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 2,
    'name' => 'changeDotOrg-targetsTpl',
    'description' => 'The tpl chunk for snippet calls to get targets data. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'targets.chunk.tpl'),
));

$chunks[2]= $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 3,
    'name' => 'changeDotOrg-updatesTpl',
    'description' => 'The tpl chunk for snippet calls to get updates data. Duplicate this to override it.',
    'snippet' => file_get_contents($sources['chunks'].'updates.chunk.tpl'),
));
return $chunks;