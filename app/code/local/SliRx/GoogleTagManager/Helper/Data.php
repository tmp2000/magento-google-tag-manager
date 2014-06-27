<?php

/**
 * Class SliRx_GoogleTagManager_Helper_Data
 */
class SliRx_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'slirx_gtm/slirx_gtm_group/enable';
    const XML_PATH_CONTAINER_ID = 'slirx_gtm/slirx_gtm_group/container_id';
    const XML_PATH_ENABLE_REMARKETING = 'slirx_gtm/slirx_gtm_group/enable_remarketing';
    const XML_PATH_ENABLE_TRANSACTION = 'slirx_gtm/slirx_gtm_group/enable_transaction';

    /**
     * Return module status
     *
     * @return mixed
     */
    public function isActive()
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Return container id
     *
     * @return mixed
     */
    public function getContainerId()
    {
        return Mage::getStoreConfig(self::XML_PATH_CONTAINER_ID);
    }

    /**
     * Return remarketing status
     *
     * @return mixed
     */
    public function isActiveRemarketing()
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLE_REMARKETING);
    }

    /**
     * Return transaction status
     *
     * @return mixed
     */
    public function isActiveTransaction()
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLE_TRANSACTION);
    }
}
