<?php


class InternetCode_Health_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function runChecks()
    {
        $results = [];

        // Check for cron job status
        $results['aoe_cron_scheduler'] = $this->_checkCronStatus(); // int

        // Check for outdated Magento version
        $results['current_version'] = $this->_checkMagentoVersion(true); // text
        $results['latest_version'] = $this->_checkMagentoVersion(false); // text

        // Check for outdated Magento version
        $results['cache_status'] = $this->_checkCacheStatus(); // text

        return $results;
    }

    private function _checkCronStatus(): int
    {
        $result = '';
        if (!Mage::helper('aoe_scheduler')->isDisabled('aoescheduler_heartbeat')) {
            $lastHeartbeat = Mage::helper('aoe_scheduler')->getLastHeartbeat();
            if ($lastHeartbeat === false) {
                return -1; //'No heartbeat task found. Check if cron is configured correctly.';
            } else {
                $timespan = Mage::helper('aoe_scheduler')->dateDiff($lastHeartbeat);
                return round($timespan / 60);
//                if ($timespan <= 5 * 60) {
//
//                } elseif ($timespan > 5 * 60 && $timespan <= 60 * 60) {
//                    return round($timespan / 60); //sprintf('Last heartbeat is older than %s minutes.', round($timespan / 60));
//                } else {
//                    return round($timespan / 60); //sprintf('Last heartbeat is older than one hour. Please check your settings and your configuration!');
//                }
            }
        }
        return $result;
    }

    private function _checkMagentoVersion($current = true): string
    {
        $currentVersion = Mage::getOpenMageVersion();
        if($current){
            return $currentVersion;
        }
        $client = new Zend_Http_Client('https://api.github.com/repos/OpenMage/magento-lts/releases/latest');
        try {
            $response = $client->request('GET');
            $responseJSON = json_decode((string) $response->getBody(), true);
            $latestVersion = preg_replace('/[a-z-A-Z]/', '', $responseJSON['tag_name']);
            return $latestVersion;
        } catch (Zend_Http_Client_Exception $e) {
            Mage::logException($e);
            return '';
        }

    }

    private function _checkCacheStatus(): string
    {
        $cacheOptions = Mage::getResourceModel('core/cache')->getAllOptions();
        $disabledCacheTypes = [];
        foreach ($cacheOptions as $cache_type => $enabled) {
            if ($enabled !== '1') {
                $disabledCacheTypes[] = $cache_type;
            }
        }
        if (count($disabledCacheTypes)) {
            return 'Disabled cache types found: ' . implode(', ', $disabledCacheTypes);
        }
        return '';
    }
}
