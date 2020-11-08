<?php

namespace Common\Base\Setup\Patch;

use Magento\Catalog\Model\Product;

trait TraitCatalogData
{
    /**
     * @var int
     */
    private $defaultProductAttributeSetId;

    /**
     * @return int
     */
    private function getDefaultProductAttributeSetId()
    {
        if ($this->defaultProductAttributeSetId === null) {
            $this->defaultProductAttributeSetId = $this->eavSetup->getDefaultAttributeSetId(Product::ENTITY);
        }
        return $this->defaultProductAttributeSetId;
    }

    /**
     * Add Product Attribute
     *
     * Structure of $attributeInfo is like:
     * <table>
     * <tr><td>'type'</td><td>=></td><td>string</td><td>// datetime, decimal, int, text, varchar</td></tr>
     * <tr><td>'label'</td><td>=></td><td>string,</td><td></td></tr>
     * <tr><td>'input'</td><td>=></td><td>string,</td><td style="white-space: nowrap;">
     * // select, text, date, hidden, boolean, multiline, textarea, image, media_image, multiselect</td></tr>
     * <tr><td>'source'</td><td>=></td><td>string,</td>
     * <td>// etc. Magento\Eav\Model\Entity\Attribute\Source\Table</td></tr>
     * <tr><td>'required'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'sort_order'</td><td>=></td><td>600,</td><td></td></tr>
     * <tr><td>'global'</td><td>=></td><td>string,</td><td>// etc. ScopedAttributeInterface::SCOPE_GLOBAL</td></tr>
     * <tr><td>'user_defined'</td><td>=></td><td>false,</td>
     * <td>// field `is_user_defined` of  table `eav_attribute`</td></tr>
     * <tr><td>'is_visible_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'is_filterable_in_grid'</td><td>=></td><td>false,</td><td></td></tr>
     * <tr><td>'visible'</td><td>=></td><td>true,</td><td>// whether to show in back-end</td></tr>
     * <tr><td>'visible_on_front'</td><td>=></td><td>false,</td>
     * <td>// whether to show in front-end product detail page</td></tr>
     * <tr><td>'used_in_product_listing'</td><td>=></td><td>false,</td>
     * <td>// whether to create in flat table</td></tr>
     * <tr><td>'used_for_sort_by'</td><td>=></td><td>false</td>
     * <td>// whether to add as an option of sort by in front-end</td></tr>
     * </table>
     *
     * @param string      $attributeCode
     * @param array       $attributeInfo
     * @param string|null $attributeGroupCode
     * @param string|null $attributeSetName
     */
    private function addProductAttribute(
        $attributeCode,
        array $attributeInfo,
        $attributeGroupCode = null,
        $attributeSetName = null
    ) {
        $this->eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attributeInfo);
        if ($attributeGroupCode) {
            $attributeSetId = $attributeSetName
                ? $this->eavSetup->getAttributeSetId(Product::ENTITY, $attributeSetName)
                : $this->getDefaultProductAttributeSetId();
            $groupId = $this->eavSetup->getAttributeGroupId(Product::ENTITY, $attributeSetId, $attributeGroupCode);
            $this->eavSetup->addAttributeToGroup(Product::ENTITY, $attributeSetId, $groupId, $attributeCode);
        }
    }
}
