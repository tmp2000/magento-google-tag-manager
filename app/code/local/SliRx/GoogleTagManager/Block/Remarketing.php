<?php

/**
 * Class SliRx_GoogleTagManager_Block_Remarketing
 */
class SliRx_GoogleTagManager_Block_Remarketing extends Mage_Core_Block_Template
{
    public function getRemarketingData()
    {
        $result = [
            'event'             => 'fireRemarketingTag',
            'google_tag_params' => []
        ];

        $pageType = $this->getPageType();

        switch ($pageType) {
            case 'home':
                $result['google_tag_params'] = [
                    'ecomm_pagetype' => $pageType
                ];

                break;

            case 'category':
                $products = Mage::getBlockSingleton('catalog/product_list')->getLoadedProductCollection();

                $ids = '';

                foreach ($products as $item) {
                    if ($ids !== '') {
                        $ids .= ', ';
                    }

                    $ids .= "'" . $item->getSku() . "'";
                }

                $result['google_tag_params'] = [
                    'ecomm_prodid'   => '[' . $ids . ']',
                    'ecomm_pagetype' => $pageType
                ];

                break;

            case 'product':
                $product = Mage::registry('current_product');

                $result['google_tag_params'] = [
                    'ecomm_prodid'     => $product->getSku(),
                    'ecomm_pagetype'   => $pageType,
                    'ecomm_totalvalue' => round($product->getPrice(), 2),
                ];

                break;

            case 'cart':
                $quote = Mage::getSingleton('checkout/cart')->getQuote();
                $grandTotal = $quote->getGrandTotal();
                $products = $quote->getAllItems();
                $ids = '';

                foreach ($products as $item) {
                    if ($ids !== '') {
                        $ids .= ', ';
                    }

                    $ids .= "'" . $item->getSku() . "'";
                }

                $result['google_tag_params'] = [
                    'ecomm_prodid'     => '[' . $ids . ']',
                    'ecomm_pagetype'   => $pageType,
                    'ecomm_totalvalue' => round($grandTotal, 2),
                ];

                break;

            case 'purchase':
                $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

                if ($orderId) {
                    $order = Mage::getModel('sales/order')->loadByAttribute('increment_id', $orderId);
                    $items = $order->getAllItems();

                    $ids = [];

                    foreach ($items as $item) {
                        $ids[] = $item->getProductId();
                    }

                    $products = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('sku')
                        ->addIdFilter($ids);

                    $ids = '';

                    foreach ($products as $item) {
                        if ($ids !== '') {
                            $ids .= ', ';
                        }

                        $ids .= "'" . $item->getSku() . "'";
                    }

                    $result['google_tag_params'] = [
                        'ecomm_prodid'     => '[' . $ids . ']',
                        'ecomm_pagetype'   => $pageType,
                        'ecomm_totalvalue' => round($order->getGrandTotal(), 2),
                    ];
                }

                break;
        }

        return json_encode($result);
    }
}
