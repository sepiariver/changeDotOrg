<?php
/**
 * changeDotOrg
 *
 * Copyright 2013 by YJ TSo <yj@modx.com>
 *
 * This file is part of changeDotOrg, a MODX integration of the Change.org API.
 *
 * changeDotOrg is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * changeDotOrg is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * changeDotOrg; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package changedotorg
*/

class ChangeDotOrg {
    /**
     * @var modX|null $modx
     */
    public $modx = null;
    /**
     * @var array
     */
    public $config = array();
    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @param \modX $modx
     * @param array $config
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('core_path').'components/changedotorg/';
        $this->config = array_merge(array(
            'basePath' => $corePath,
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'elementsPath' => $corePath.'elements/',
        ),$config);

        $modelPath = $this->config['modelPath'];
        $this->modx->addPackage('changedotorg',$modelPath);
    }

    /**
     * Grab response data (from cache if possible).
     * @return array|mixed
     */
    public function getPetitionData($id, $apiKey, $requested = 'signatures', $cacheExpires = '7200') {
        /* Attempt to get from cache */
        $cacheId = $requested . '.' . $id;
        $cacheOptions = array(
          xPDO::OPT_CACHE_HANDLER => $this->modx->getOption('cache_handler'),
          xPDO::OPT_CACHE_KEY => $this->modx->getOption('changedotorg_cache_key',null,'changedotorg'),
          xPDO::OPT_CACHE_EXPIRES => $cacheExpires,
        );

        $data = $this->modx->getCacheManager()->get($cacheId, $cacheOptions);

        if (empty($data)) {
          $apiUrl = 'https://api.change.org/v1/petitions/' 
            . $id 
            . '/'
            . $requested
            . '?api_key=' 
            . $apiKey;
            
          $response = file_get_contents($apiUrl);
          $data = $this->modx->fromJSON($response);

          /* Write to cache again */
          $this->modx->cacheManager->set($cacheId, $data, $cacheExpires, $cacheOptions);
        }

        return $data;
    }
    /**
     * Grab petitionId.
     * @return string
     */
    public function getPetitionId($url, $apiKey) {
        //First check if the setting exists
        $setting = $this->modx->getObject('modSystemSetting', 'changedotorg_petition_id');

        // Create the System Setting if it doesn't exist
        if (!$setting) {
            $newSetting = $this->modx->newObject('modSystemSetting');
            $newSetting->set('key', 'changedotorg_petition_id');
            $newSetting->set('xtype', 'textfield');
            $newSetting->set('area', 'changedotorg');
 
            $newSetting->save();
            $setting = $this->modx->getObject('modSystemSetting', 'changedotorg_petition_id');
        }
        
        $petitionId = $setting->get('value');
        if(empty($petitionId)) {
            // API requirements
            $apiUrl = 'https://api.change.org/v1/petitions/get_id';
    
            // Build query
            $params = array(
                'api_key' => $apiKey,
                'petition_url' => $url,
            );
            $query = http_build_query($params);
    
            // Request
            $requestUrl = "$apiUrl?$query";

            // Response
            $response = file_get_contents($requestUrl);
            $petition = $this->modx->fromJSON($response);
    
            // Get petition ID
            $petitionId = $petition['petition_id'];
    
            // Save it in System Setting
            $setting->set('value', $petitionId);
            $setting->save();
     
            // Clear the cache:
            $cacheRefreshOptions =  array( 'system_settings' => array() );
            $this->modx->cacheManager->refresh($cacheRefreshOptions);       
        }
        return $petitionId;
    }
}