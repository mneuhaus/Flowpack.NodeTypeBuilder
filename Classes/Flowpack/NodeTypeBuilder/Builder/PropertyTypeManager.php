<?php
namespace Flowpack\NodeTypeBuilder\Builder;


/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\NodeTypeBuilder\Builder\PropertyType;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class PropertyTypeManager {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Node types, indexed by name
	 *
	 * @var array
	 */
	protected $cachedPropertyTypes = array();

	public function getPropertyTypes() {
		$this->loadPropertyTypes();
		return $this->cachedPropertyTypes;
	}

	public function getPropertyType($propertyType) {
		$this->loadPropertyTypes();
		return $this->cachedPropertyTypes[$propertyType];
	}

	/**
	 * Loads all node types into memory.
	 *
	 * @return void
	 */
	protected function loadPropertyTypes() {
		if (empty($this->cachedPropertyTypes)) {
			$completePropertyTypeConfiguration = $this->configurationManager->getConfiguration('PropertyTypes');
			foreach (array_keys($completePropertyTypeConfiguration) as $propertyTypeName) {
				$this->loadPropertyType($propertyTypeName, $completePropertyTypeConfiguration);
			}
		}
	}

	/**
	 * Load one node type, if it is not loaded yet.
	 *
	 * @param string $propertyTypeName
	 * @param array $completePropertyTypeConfiguration the full node type configuration for all node types
	 * @return
	 */
	protected function loadPropertyType($propertyTypeName, array $completePropertyTypeConfiguration) {
		if (isset($this->cachedPropertyTypes[$propertyTypeName])) {
			return $this->cachedPropertyTypes[$propertyTypeName];
		}

		if (!isset($completePropertyTypeConfiguration[$propertyTypeName])) {
			throw new \TYPO3\TYPO3CR\Exception('Property type "' . $propertyTypeName . '" does not exist', 1316451800);
		}

		$propertyTypeConfiguration = $completePropertyTypeConfiguration[$propertyTypeName];

		$mergedConfiguration = array();
		$superTypes = array();
		if (isset($propertyTypeConfiguration['superTypes'])) {
			foreach ($propertyTypeConfiguration['superTypes'] as $superTypeName) {
				$superType = $this->loadPropertyType($superTypeName, $completePropertyTypeConfiguration);
				$superTypes[] = $superType;
				$mergedConfiguration = \TYPO3\Flow\Utility\Arrays::arrayMergeRecursiveOverrule($mergedConfiguration, $superType->getConfiguration());
			}
			unset($mergedConfiguration['superTypes']);
		}

		$mergedConfiguration = \TYPO3\Flow\Utility\Arrays::arrayMergeRecursiveOverrule($mergedConfiguration, $propertyTypeConfiguration);

		$propertyType = new PropertyType($propertyTypeName, $superTypes, $mergedConfiguration);

		$this->cachedPropertyTypes[$propertyTypeName] = $propertyType;
		return $propertyType;
	}
}
?>