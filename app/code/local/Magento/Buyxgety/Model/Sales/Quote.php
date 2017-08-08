<?php
class Magento_Buyxgety_Model_Sales_Quote extends Mage_Sales_Model_Quote
{
	public function getItemByProduct($product)
    {
    	$MultilineAddingObserver = Mage::getSingleton('core/session')->getMultilineAddingObserver();
		if($MultilineAddingObserver == 1){
			Mage::getSingleton('core/session')->unsMultilineAddingObserver();
			return false;
    	}else{
	        foreach ($this->getAllItems() as $item) {
	            if ($item->representProduct($product)) {
	                return $item;
	            }
	        }
        	return false;
    	}
    }
}

?>