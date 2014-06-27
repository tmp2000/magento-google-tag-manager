<?php

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
        $data = [];

        if ($this->_orderId) {
            $order = Mage::getModel('sales/order')->loadByAttribute('increment_id', $this->_orderId);
            $items = $order->getAllItems();
            $products = $this->_getProducts($items);

            // calculation price of all products
            $priceAll = 0;
            foreach ($products as $item) {
                $priceAll += $item['price'] * $item['quantity'];
            }

            $data = [
                'transactionId'          => $order->getIncrementId(),
                'transactionAffiliation' => 'Kolesiko',
                'transactionTotal'       => $priceAll,
                'transactionTax'         => '',
                'transactionShipping'    => round($order->getShippingAmount(), 2),
                'transactionProducts'    => $products
            ];
        }

        $data = json_encode($data);

        return $data;
    }

    protected function _getProducts($items)
    {
        $products = [];

        $tmpItems = [];
        // add products id to array
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
            $tmpItems[$item->getProductId()] = $item;
        }

        $productsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(['cost', 'price', 'name', 'sku'])
            ->addIdFilter($ids);

        foreach ($productsCollection as $product) {
            $price = $product->getPrice() - $product->getCost();

            $products[] = [
                'sku'      => $product->getSku(),
                'name'     => $product->getName(),
//                'category' => '',
                'price'    => round($price, 2),
                'quantity' => (int)$tmpItems[$product->getId()]->getQty_ordered()
            ];
        }

        return $products;
    }
}
