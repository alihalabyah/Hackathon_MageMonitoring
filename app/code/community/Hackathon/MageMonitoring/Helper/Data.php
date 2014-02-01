<?php

class Hackathon_MageMonitoring_Helper_Data extends Mage_Core_Helper_Data {

    public function getActiveCaches() {

        // @todo: add caching mechanism (core_config_data with rescan button in backend?)

        // load all classes in Model/CacheStats
        $implFolder = Mage::getModuleDir(null, 'Hackathon_MageMonitoring') . DS . 'Model' . DS . 'CacheStats';
        foreach(array_filter(glob($implFolder."/*"), 'is_file') as $f) {
            require_once $f;
        }

        // get classes implementing cachestats interface
        $iName = 'Hackathon_MageMonitoring_Model_CacheStats';
        if (interface_exists($iName)) {
            $cacheClasses = array_filter(get_declared_classes(),
                     create_function('$className', "return in_array(\"$iName\", class_implements(\"\$className\"));"));
        }

        // collect active caches
        foreach ($cacheClasses as $cache) {
            $c = new $cache();
            if ($c->isActive()) {
                $activeCaches[] = $c;
            }
        }

        return $activeCaches;
    }
    
    /**
     * @param string $value
     * @param bool $inMegabytes
     * @return int|string
     */
    public function getValueInByte($value, $inMegabytes = false)
    {
        $memoryLimit = trim($value);

        $lastMemoryLimitLetter = strtolower(substr($memoryLimit, -1));
        switch($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        if ($inMegabytes) {
            $memoryLimit = ($memoryLimit / 1024) / 1024;
        }

        return $memoryLimit;
    }

}