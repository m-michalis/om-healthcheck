<?php


class InternetCode_Health_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function runChecks()
    {
        $results = [];

        // Check for cron job status
        $results['aoe_cron_scheduler'] = $this->_checkCronStatus();

        // Check for outdated Magento version
        $results['magento_version'] = $this->_checkMagentoVersion();

        // Check for outdated Magento version
        $results['cache_status'] = $this->_checkCacheStatus();

        return $results;
    }

    private function _checkCronStatus(): array
    {
        $result = [
            'success' => true
        ];
        if (!Mage::helper('aoe_scheduler')->isDisabled('aoescheduler_heartbeat')) {
            $lastHeartbeat = Mage::helper('aoe_scheduler')->getLastHeartbeat();
            if ($lastHeartbeat === false) {
                $result = [
                    'success' => false,
                    'message' => 'No heartbeat task found. Check if cron is configured correctly.'
                ];
            } else {
                $timespan = Mage::helper('aoe_scheduler')->dateDiff($lastHeartbeat);
                if ($timespan <= 5 * 60) {

                } elseif ($timespan > 5 * 60 && $timespan <= 60 * 60) {
                    $result = [
                        'success' => false,
                        'message' => sprintf('Last heartbeat is older than %s minutes.', round($timespan / 60))
                    ];
                } else {
                    $result = [
                        'success' => false,
                        'message' => sprintf('Last heartbeat is older than one hour. Please check your settings and your configuration!')
                    ];
                }
            }
        }
        return $result;
    }

    private function _checkMagentoVersion(): array
    {
        $currentVersion = Mage::getOpenMageVersion();

        $client = new Zend_Http_Client('https://api.github.com/repos/OpenMage/magento-lts/releases/latest');
        try {
            $response = $client->request('GET');
            $responseJSON = json_decode((string) $response->getBody(), true);
            $latestVersion = preg_replace('/[a-z-A-Z]/', '', $responseJSON['tag_name']);
            if (version_compare($latestVersion, $currentVersion, '>')) {
                return [
                    'success' => false,
                    'message' => "New OpenMage version available: $latestVersion. Current version: $currentVersion"
                ];
            } else {
                return [
                    'success' => true
                ];
            }
        } catch (Zend_Http_Client_Exception $e) {
            Mage::logException($e);
            return [
                'success' => false,
                'message' => 'Could not retrieve latest version. Check logs.'
            ];
        }

    }

    private function _checkCacheStatus()
    {
        $cacheOptions = Mage::getResourceModel('core/cache')->getAllOptions();
        $result = [
            'success' => true,
        ];
        $disabledCacheTypes = [];
        foreach ($cacheOptions as $cache_type => $enabled) {
            if ($enabled !== '1') {
                $disabledCacheTypes[] = $cache_type;
                $result['success'] = false;
            }
        }
        if (!$result['success']) {
            $result['message'] = 'Disabled cache types found: ' . implode(', ', $disabledCacheTypes);
        }
        return $result;
    }
}
