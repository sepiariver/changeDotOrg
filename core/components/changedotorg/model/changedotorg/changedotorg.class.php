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

class changeDotOrg {
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

        $corePath = $this->modx->getOption('core_path').'components/changedotorg/');
        $this->config = array_merge(array(
            'basePath' => $corePath,
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'elementsPath' => $corePath.'elements/',
        ),$config);

        $modelPath = $this->config['modelPath'];
        $this->modx->addPackage('changedotorg',$modelPath);
        
        $this->debug = (bool)$this->modx->getOption('changedotorg.debug',null,false);
    }

    /**
     * Grab settings (from cache if possible) as key => value pairs.
     * @return array|mixed
     */
    public function getSettings($criteria = NULL, $cacheId = 'clientconfig', $cacheKey = 'system_settings') {
        /* Attempt to get from cache */
        $cacheOptions = array(xPDO::OPT_CACHE_KEY => $cacheKey);
        $settings = $this->modx->getCacheManager()->get($cacheId, $cacheOptions);

        if (empty($settings) && $this->modx->getCount('cgSetting') > 0) {
            $collection = $this->modx->getCollection('cgSetting', $criteria);
            $settings = array();
            /* @var cgSetting $setting */
            foreach ($collection as $setting) {
                $settings[$setting->get('key')] = $setting->get('value');
            }
            /* Write to cache again */
            $this->modx->cacheManager->set($cacheId, $settings, 0, $cacheOptions);
        }

        return (is_array($settings)) ? $settings : array();
    }

    /**
     * Indicates if the logged in user has admin permissions.
     * @return bool
     */
    public function hasAdminPermission() {
        if (!$this->modx->user || ($this->modx->user->get('id') < 1)) {
            return false;
        }

        $usergroups = $this->modx->getOption('clientconfig.admin_groups', null, 'Administrator');
        $usergroups = explode(',', $usergroups);

        $isMember = $this->modx->user->isMember($usergroups, false);

        /* If we're not a member of the usergroup(s), check for sudo */
        if (!$isMember) {
            $v = $this->modx->getVersionData();
            if (version_compare($v['full_version'], '2.2.1-pl') == 1) {
                $isMember = (bool)$this->modx->user->get('sudo');
            }
        }
        return $isMember;
    }
}

