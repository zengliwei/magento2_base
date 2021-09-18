<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Setup\Patch;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute as ResourceCustomerAttribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;
use Zend_Validate_Exception;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
trait TraitCustomerData
{
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @var int
     */
    private $defaultCustomerAttributeSetId;

    /**
     * @var ResourceCustomerAttribute
     */
    private $resourceCustomerAttribute;

    /**
     * @return int
     */
    private function getDefaultCustomerAttributeSetId()
    {
        if ($this->defaultCustomerAttributeSetId === null) {
            $this->defaultCustomerAttributeSetId = $this->eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        }
        return $this->defaultCustomerAttributeSetId;
    }

    /**
     * @return ResourceCustomerAttribute
     */
    private function getResourceCustomerAttribute()
    {
        if ($this->resourceCustomerAttribute === null) {
            $this->resourceCustomerAttribute = $this->objectManager->get(ResourceCustomerAttribute::class);
        }
        return $this->resourceCustomerAttribute;
    }

    /**
     * Add Customer Attribute
     *
     * Structure of $attributeInfo is like:
     * <table>
     * <tr><td>'type'</td><td>=></td><td>string</td><td>// datetime, decimal, int, text, varchar</td></tr>
     * <tr><td>'label'</td><td>=></td><td>string,</td><td></td></tr>
     * <tr><td>'input'</td><td>=></td><td>string,</td><td style="white-space: nowrap;">
     * // select, text, date, hidden, boolean, multiline, textarea, image, media_image, multiselect</td></tr>
     * <tr><td>'source'</td><td>=></td><td>string,</td>
     * <td>// etc. Magento\Eav\Model\Entity\Attribute\Source\Boolean</td></tr>
     * <tr><td>'required'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'sort_order'</td><td>=></td><td>600,</td><td></td></tr>
     * <tr><td>'user_defined'</td><td>=></td><td>false,</td>
     * <td>// field `is_user_defined` of  table `eav_attribute`</td></tr>
     * <tr><td>'system'</td><td>=></td><td>false,</td><td style="white-space: nowrap;">
     * // field `is_system` of  table `customer_eav_attribute`</td></tr>
     * <tr><td>'visible'</td><td>=></td><td>true,</td><td>// whether to show in back-end</td></tr>
     * <tr><td>'is_used_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'is_visible_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'is_filterable_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'is_searchable_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * </table>
     *
     * @param string   $attributeCode
     * @param array    $attributeInfo
     * @param string[] $forms
     * @param string   $attributeGroupCode
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    private function addCustomerAttribute(
        $attributeCode,
        array $attributeInfo,
        $forms = [
            'adminhtml_checkout',
            'adminhtml_customer',
            //'adminhtml_customer_address',
            'customer_account_create',
            'customer_account_edit',
            //'customer_address_edit',
            //'customer_register_address'
        ],
        $attributeGroupCode = 'general'
    ) {
        $this->eavSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeInfo);

        /**
         * Both 'customer_account_edit' and 'adminhtml_customer' are required if we need
         *     to show the attribute in backend customer edit page.
         */
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);
        $attribute->setData('used_in_forms', $forms);
        $this->getResourceCustomerAttribute()->save($attribute);

        $attributeGroupId = (int)$this->eavSetup->getAttributeGroupByCode(
            Customer::ENTITY,
            $this->getDefaultCustomerAttributeSetId(),
            $attributeGroupCode,
            'attribute_group_id'
        );

        $this->eavSetup->addAttributeToSet(
            Customer::ENTITY,
            $this->getDefaultCustomerAttributeSetId(),
            $attributeGroupId,
            $attributeCode
        );
    }
}
