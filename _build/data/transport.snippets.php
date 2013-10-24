<?php
$snippets = array();

/* create the plugin object */
$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->set('id',1);
$snippets[0]->set('name','changeDotOrg');
$snippets[0]->set('description','Returns changeDotOrg petition data.');
$snippets[0]->set('snippet', getSnippetContent($sources['snippets'] . 'changeDotOrg.snippet.php'));
$snippets[0]->set('category', 'changeDotOrg');

return $snippets;
