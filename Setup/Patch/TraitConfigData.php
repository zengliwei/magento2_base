<?php

namespace Common\Base\Setup\Patch;

use Exception;
use Magento\Config\Model\Config;
use Magento\Config\Model\Config\Structure;

trait TraitConfigData
{
    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @param string $section    e.g.
     * @param array  $groupData  e.g. []
     * @param string $website
     * @param string $store
     * @throws Exception
     */
    private function saveConfig($section, array $groupData, $website, $store)
    {
        // filter methods are copied from \Magento\Config\Controller\Adminhtml\System\Config\Save
        $data = $this->filterNodes(
            [
                'section' => $section,
                'groups'  => $groupData,
                'website' => $website,
                'store'   => $store
            ]
        );
        /* @var $config Config */
        $config = $this->objectManager->create(Config::class, ['data' => $data]);
        $config->save();
    }

    /**
     * @param array $configData
     * @return array
     */
    private function filterNodes(array $configData): array
    {
        if (!empty($configData['groups'])) {
            $fieldPaths = $this->getConfigStructure()->getFieldPaths();
            $systemXmlPathsFromKeys = array_keys($fieldPaths);
            $systemXmlPathsFromValues = array_reduce(array_values($fieldPaths), 'array_merge', []);
            $systemXmlConfig = array_merge($systemXmlPathsFromKeys, $systemXmlPathsFromValues);
            $configData['groups'] = $this->filterPaths($configData['section'], $configData['groups'], $systemXmlConfig);
        }
        return $configData;
    }

    /**
     * @return Structure
     */
    private function getConfigStructure()
    {
        if ($this->configStructure === null) {
            $this->objectManager->get(Structure::class);
        }
        return $this->configStructure;
    }

    /**
     * @param string   $prefix           Path prefix
     * @param array    $groups           Groups data
     * @param string[] $systemXmlConfig  Defined paths
     * @return array Filtered groups
     */
    private function filterPaths(string $prefix, array $groups, array $systemXmlConfig): array
    {
        $flippedXmlConfig = array_flip($systemXmlConfig);
        $filtered = [];
        foreach ($groups as $groupName => $childPaths) {
            $filtered[$groupName] = ['fields' => [], 'groups' => []];
            if (isset($childPaths['fields'])) {
                foreach ($childPaths['fields'] as $field => $fieldData) {
                    $path = $prefix . '/' . $groupName . '/' . $field;
                    $element = $this->getConfigStructure()->getElement($path);
                    if ($element
                        && ($elementData = $element->getData())
                        && isset($elementData['config_path'])
                    ) {
                        $path = $elementData['config_path'];
                    }
                    if (isset($flippedXmlConfig[$path])) {
                        $filtered[$groupName]['fields'][$field] = $fieldData;
                    }
                }
            }
            if (!empty($childPaths['groups'])) {
                $filteredGroups = $this->filterPaths(
                    $prefix . '/' . $groupName,
                    $childPaths['groups'],
                    $systemXmlConfig
                );
                if ($filteredGroups) {
                    $filtered[$groupName]['groups'] = $filteredGroups;
                }
            }
            $filtered[$groupName] = array_filter($filtered[$groupName]);
        }
        return array_filter($filtered);
    }
}
