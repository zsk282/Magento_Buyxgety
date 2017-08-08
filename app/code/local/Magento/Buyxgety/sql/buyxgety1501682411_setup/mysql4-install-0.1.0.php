<?php
 
$installer = $this;
// $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
// $installer->startSetup();
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
/**
 * Adding Different Attributes
 */
 
// adding attribute group
$setup->addAttributeGroup('catalog_product', 'Default', 'Buy X Get Y', 1000);
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page

$setup->addAttribute('catalog_product', 'buyxgety', array(
	'group'         => 'Buy X Get Y',
	'label'             => 'Buy X Get Y',
	'type'              => 'varchar',
	'input'             => 'select',
	'backend'           => 'eav/entity_attribute_backend_array',
	'frontend'          => '',    
	'source'            => '',
	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible'           => true,
	'required'          => false,
	'user_defined'      => true,
	'searchable'        => false,
	'filterable'        => false,
	'comparable'        => false,
	'option'            => array ('value' => array('1' => array('Enable'),
	                     '0' => array('Disable'),
	                )
	            ),
	'visible_on_front'  => false,
	'visible_in_advanced_search' => false,
	'unique'            => false
));

$setup->addAttribute('catalog_product', 'buyxgety_xqty', array(
    'group'         => 'Buy X Get Y',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'X Product Qty',
    'backend'       => '',
    'visible'       => 1,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'buyxgety_ysku', array(
    'group'         => 'Buy X Get Y',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Y SKU',
    'backend'       => '',
    'visible'       => 1,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'buyxgety_yqty', array(
    'group'         => 'Buy X Get Y',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Y qty',
    'backend'       => '',
    'visible'       => 1,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
 $setup->addAttribute('catalog_product', 'buyxgety_ydiscount', array(
    'group'         => 'Buy X Get Y',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Discount on Y',
    'backend'       => '',
    'visible'       => 1,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();
