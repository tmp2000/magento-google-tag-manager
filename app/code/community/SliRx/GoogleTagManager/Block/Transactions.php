<?php

/**
 * @author     Karazey Sergey <karazey.sergey@gmail.com>
 * @copyright  2014 Karazey Sergey
 * @created    10:00 27/06/2014
 */

/**
 * Class SliRx_GoogleTagManager_Block_Transactions
 */
class SliRx_GoogleTagManager_Block_Transactions extends Mage_Checkout_Block_Success
{
    protected $_orderId = 0;

    function __construct()
    {
        $this->_orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }

    public function getTransactionsData()
    {
        $helper = Mage::helper('slirx_google_tag_manager');
        $data = array();

        if ($this->_orderId) {
            $order = Mage::getModel('sales/order')->loadByAttribute('increment_id', $this->_orderId);
            $items = $order->getAllItems();
            $products = $this->_getProducts($items);

            // calculation price of all products
            $priceAll = 0;
            foreach ($products as $item) {
                $priceAll += $item['price'] * $item['quantity'];
            }

            $data = array(
                'transactionId'          => $order->getIncrementId(),
                'transactionAffiliation' => $helper->getTransactionAffiliation(),
                'transactionTotal'       => $priceAll,
                'transactionTax'         => '',
                'transactionShipping'    => round($order->getShippingAmount(), 2),
                'transactionProducts'    => $products
            );
        }

        $data = json_encode($data);

        return $data;
    }

    protected function _getProducts($items)
    {
        $products = array();

        $tmpItems = array();
        // add products id to array
        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
            $tmpItems[$item->getProductId()] = $item;
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(array('cost', 'price', 'name', 'sku'))
            ->addIdFilter($ids);

        foreach ($productsCollection as $product) {
            $price = $product->getPrice() - $product->getCost();

            $products[] = array(
                'sku'      => $product->getSku(),
                'name'     => $product->getName(),
                //                'category' => '',
                'price'    => round($price, 2),
                'quantity' => (int)$tmpItems[$product->getId()]->getQty_ordered()
            );
        }

        return $products;
    }
}
