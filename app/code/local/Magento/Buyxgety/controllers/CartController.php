<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Magento_Buyxgety_CartController extends Mage_Checkout_CartController
{
	public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            // code for buyxgety
            $this->magicMethod();
            // buyxgetyEnds


            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

        protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)->save();

                $this->magicMethod();
            
            // die;
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    // Method for Buy X get Y free 
    public function magicMethod(){
        $cart = $this->_getCart();
        $quoteArrayFreeProducts = array();
        $quoteArrayNonFreeProducts = array();
        $AddThisInCart = array();
        $finalAdd = array();

        // finding both free and non free products and saving them in array
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllVisibleItems() as $item) {
            if($item->getData('price') == 0){
                $quoteArrayFreeProducts['item_id'][] = $item->getData('product_id');
                $quoteArrayFreeProducts['qty'][] = $item->getData('qty');
            }else{
                $quoteArrayNonFreeProducts['item_id'][] = $item->getData('product_id');
                $quoteArrayNonFreeProducts['qty'][] = $item->getData('qty');
            }
        }
        
        // print_r($quoteArrayFreeProducts);die;
        // finding free associatied produts and adding them in another array
        for($i = 0; $i < count($quoteArrayNonFreeProducts['item_id']) ;$i++){
            $product = Mage::getModel('catalog/product')->load($quoteArrayNonFreeProducts['item_id'][$i]);
            // print_r($product->getAttributeText('buyxgety'));die;
            if($product->getAttributeText('buyxgety') == 'Enable'){
                $Buyxgety_xqty = $product->getBuyxgety_xqty();
                $Buyxgety_ysku = $product->getBuyxgety_ysku();
                $Buyxgety_yqty = $product->getBuyxgety_yqty();

                // $Buyxgety_ydiscount = $product->getBuyxgety_ydiscount();
                if(!empty($Buyxgety_xqty) && !empty($Buyxgety_ysku) && !empty($Buyxgety_yqty) ){
                    // die($Buyxgety_ysku);
                    $AddThisInCart['item_id'][] = Mage::getModel('catalog/product')->getIdBySku($Buyxgety_ysku);
                    $AddThisInCart['qty'][] = (int)($quoteArrayNonFreeProducts['qty'][$i]/$Buyxgety_xqty)*$Buyxgety_yqty;
                }
            }
        }
        for($i = 0; $i < count($AddThisInCart['item_id']) ;$i++){
            if(isset($quoteArrayFreeProducts['item_id'])){
                for($j = 0; $j < count($quoteArrayFreeProducts['item_id']) ;$j++){
                    if($AddThisInCart['item_id'][$i] == $quoteArrayFreeProducts['item_id'][$j]){
                        $finalAdd['item_id'][] = $AddThisInCart['item_id'][$i];
                        $finalAdd['qty'][] = $AddThisInCart['qty'][$i] - $quoteArrayFreeProducts['qty'][$j];
                    }else{
                        $finalAdd['item_id'][] = $AddThisInCart['item_id'][$i];
                        $finalAdd['qty'][] = $AddThisInCart['qty'][$i];
                    }
                }
           }else{
                $finalAdd['item_id'][] = $AddThisInCart['item_id'][$i];
                $finalAdd['qty'][] = $AddThisInCart['qty'][$i];
           }
        }

        for($i = 0; $i < count($finalAdd['item_id']) ;$i++){
            for($j = 0; $j < count($quoteArrayNonFreeProducts['item_id']); $j++){
                if($finalAdd['item_id'][$i] == $quoteArrayNonFreeProducts['item_id'][$j]){
                    foreach ($quoteArrayFreeProducts['item_id'] as $value) {
                        if($value == $finalAdd['item_id'][$i]){
                            $flag = 1;
                        }else{
                            $flag = 0;
                        }
                    }
                    if($flag == 1){
                        $finalAdd['new_row'][] = 0;
                    }else{
                        $finalAdd['new_row'][] = 1;
                    }
                }
            }
            if(!empty($quoteArrayFreeProducts['item_id'])){
                for($j = 0; $j < count($quoteArrayFreeProducts['item_id']); $j++){
                    if($finalAdd['item_id'][$i] == $quoteArrayFreeProducts['item_id'][$j]){
                        $finalAdd['new_row'][] = 0;
                    }else{
                        $finalAdd['new_row'][] = 1;
                    }
                }
            }else{
                $finalAdd['new_row'][] = 1;
            }  
        }

        // print_r($finalAdd);die;

        if(isset($finalAdd['item_id'])){
            for($i = 0; $i < count($finalAdd['item_id']) ;$i++){
                if($finalAdd['qty'][$i] > 0){
                    Mage::getSingleton('core/session')->setMultilineAddingObserver($finalAdd['new_row'][$i]);
                    Mage::getSingleton('core/session')->setZeroSettingObserver(1);
                    if($finalAdd['new_row'][$i] == 0){
                        $cartHelper = Mage::helper('checkout/cart');
                        $items = $cartHelper->getCart()->getItems();        
                        foreach ($items as $item) 
                        {
                            $itemId = $item->getItemId();
                            if($item->getProductId() == $finalAdd['item_id'][$i] &&  $item->getPrice() == 0){
                                $cartHelper->getCart()->removeItem($itemId)->save();
                                $this->magicMethod();
                            }
                        }
                    }else{
                        $productToAdd = $product->load($finalAdd['item_id'][$i]);
                        $params['qty'] = $finalAdd['qty'][$i];
                        $params['product'] = $finalAdd['item_id'][$i];
                        $cart->addProduct($productToAdd, $params);
                        $cart->save();
                    }
                }else if($finalAdd['qty'][$i] < 0){
                    $cartHelper = Mage::helper('checkout/cart');
                    $items = $cartHelper->getCart()->getItems();        
                    foreach ($items as $item) 
                    {
                        $itemId = $item->getItemId();
                        if($item->getProductId() == $finalAdd['item_id'][$i] &&  $item->getPrice() == 0){
                            $cartHelper->getCart()->removeItem($itemId)->save();
                            $this->_updateShoppingCart();
                        }
                    } 
                }
            }
        }
    }
}
