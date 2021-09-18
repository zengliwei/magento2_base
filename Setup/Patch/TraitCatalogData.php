<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Setup\Patch;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
trait TraitCatalogData
{
    protected ?int $defaultCatalogCategoryAttributeSetId = null;
    protected ?int $defaultProductAttributeSetId = null;

    /**
     * @var EavSetup
     */
    protected $eavSetup;

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
     * @param string $attributeCode
     * @param array  $attributeInfo
     * @param string $attributeGroupCode
     * @return void
     * @throws Exception
     */
    private function addCatalogCategoryAttribute(
        $attributeCode,
        array $attributeInfo,
        $attributeGroupCode = 'general'
    ) {
        $this->eavSetup->addAttribute(Category::ENTITY, $attributeCode, $attributeInfo);
        $attributeSetId = $this->getDefaultCatalogCategoryAttributeSetId();
        $groupId = $this->eavSetup->getAttributeGroupId(Category::ENTITY, $attributeSetId, $attributeGroupCode);
        $this->eavSetup->addAttributeToGroup(Category::ENTITY, $attributeSetId, $groupId, $attributeCode);
    }

    /**
     * @return int
     */
    private function getDefaultCatalogCategoryAttributeSetId()
    {
        if ($this->defaultCatalogCategoryAttributeSetId === null) {
            $this->defaultCatalogCategoryAttributeSetId = $this->eavSetup->getDefaultAttributeSetId(Category::ENTITY);
        }
        return $this->defaultCatalogCategoryAttributeSetId;
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
     * @return void
     * @throws Exception
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
     * This method creates necessary groups and assigns required attributes to the new set after creating
     *
     * @param string $attributeSetName
     * @throws LocalizedException
     */
    private function addProductAttributeSet($attributeSetName)
    {
        $this->eavSetup->addAttributeSet(Product::ENTITY, $attributeSetName);
        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, $attributeSetName);

        $attributeGroups = [
            [
                'attribute_group_name' => 'Product Details',
                'tab_group_code'       => 'basic',
                'attribute_codes'      => [
                    'status',
                    'name',
                    'sku',
                    'sku_type',
                    'price',
                    'price_type',
                    'tax_class_id',
                    'quantity_and_stock_status',
                    'weight',
                    'weight_type',
                    'visibility',
                    'category_ids',
                    'news_from_date',
                    'news_to_date',
                    'country_of_manufacture'
                ]
            ],
            [
                'attribute_group_name' => 'Content',
                'tab_group_code'       => 'basic',
                'attribute_codes'      => ['description', 'short_description']
            ],
            [
                'attribute_group_name' => 'Image Management',
                'tab_group_code'       => 'basic',
                'attribute_codes'      => [
                    'image',
                    'small_image',
                    'thumbnail',
                    'swatch_image',
                    'media_gallery',
                    'gallery'
                ]
            ],
            [
                'attribute_group_name' => 'Search Engine Optimization',
                'tab_group_code'       => 'basic',
                'attribute_codes'      => ['url_key', 'meta_title', 'meta_keyword', 'meta_description']
            ],
            [
                'attribute_group_name' => 'Advanced Pricing',
                'tab_group_code'       => 'advanced',
                'attribute_codes'      => [
                    'special_price',
                    'special_from_date',
                    'special_to_date',
                    'cost',
                    'tier_price',
                    'price_view',
                    'msrp',
                    'msrp_display_actual_price_type'
                ]
            ],
            [
                'attribute_group_name' => 'Design',
                'tab_group_code'       => 'advanced',
                'attribute_codes'      => ['page_layout', 'options_container', 'custom_layout_update_file']
            ],
            [
                'attribute_group_name' => 'Schedule Design Update',
                'tab_group_code'       => 'advanced',
                'attribute_codes'      => ['custom_design_from', 'custom_design_to', 'custom_design', 'custom_layout']
            ],
            [
                'attribute_group_name' => 'Gift Options',
                'tab_group_code'       => 'advanced',
                'attribute_codes'      => ['gift_message_available']
            ]
        ];
        $sortOrder = 10;
        foreach ($attributeGroups as $attributeGroup) {
            $this->eavSetup->addAttributeGroup(
                Product::ENTITY,
                $attributeSetName,
                $attributeGroup['attribute_group_name'],
                $sortOrder += 10
            );
            $this->eavSetup->updateAttributeGroup(
                Product::ENTITY,
                $attributeSetId,
                $attributeGroup['attribute_group_name'],
                'tab_group_code',
                $attributeGroup['tab_group_code']
            );
            $attrSortOrder = 10;
            foreach ($attributeGroup['attribute_codes'] as $attributeCode) {
                $this->eavSetup->addAttributeToGroup(
                    Product::ENTITY,
                    $attributeSetId,
                    $attributeGroup['attribute_group_name'],
                    $attributeCode,
                    $attrSortOrder += 10
                );
            }
        }
    }
}
