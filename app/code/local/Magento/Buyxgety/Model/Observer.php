<?php
class Magento_Buyxgety_Model_Observer
{

	public function updateOrder(Varien_Event_Observer $observer)
	{
		//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
		//$user = $observer->getEvent()->getUser();
		//$user->doSomething();
	}

	public function buyxgety(Varien_Event_Observer $observer)
	{
		   
	}

	public function zeroMaker(Varien_Event_Observer $observer){
		$ZeroSettingObserver = Mage::getSingleton('core/session')->getZeroSettingObserver();
		if($ZeroSettingObserver == 1){
			$item = $observer->getQuoteItem();
			$item->setCustomPrice(0);
			$item->setOriginalCustomPrice(0);
			$item->getProduct()->setIsSuperMode(true);
			Mage::getSingleton('core/session')->unsZeroSettingObserver();
		}	
	}

	public function removebuyxgety(Varien_Event_Observer $observer)
	{
        $RemoveFromCart = '';
		
		$product = Mage::getModel('catalog/product')->load($observer->getQuoteItem()->getProductId());
        if($product->getBuyxgety() == 233){
            $Buyxgety_xqty = $product->getBuyxgety_xqty();
            $Buyxgety_ysku = $product->getBuyxgety_ysku();
            $Buyxgety_yqty = $product->getBuyxgety_yqty();
            $Buyxgety_ydiscount = $product->getBuyxgety_ydiscount();
            if(!empty($Buyxgety_xqty) && !empty($Buyxgety_ysku) && !empty($Buyxgety_yqty) && !empty($Buyxgety_ydiscount)){
                $RemoveFromCart = Mage::getModel('catalog/product')->getIdBySku($Buyxgety_ysku);
            }
        }

        if(!empty($RemoveFromCart)){
	        $quote = Mage::getSingleton('checkout/session')->getQuote();
	        foreach($quote->getAllVisibleItems() as $item) {
	            if($item->getData('price') == 0 && $item->getData('product_id') == $RemoveFromCart){
					$cartHelper = Mage::helper('checkout/cart');
					$cartHelper->getCart()->removeItem($item->getData('item_id'))->save();
	            }
	        }
        }
    }
		
}
