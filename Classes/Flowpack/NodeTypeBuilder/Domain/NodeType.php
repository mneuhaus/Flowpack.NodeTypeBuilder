<?php
namespace Flowpack\NodeTypeBuilder\Domain;


/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A Node Type
 *
 * Although methods contained in this class belong to the public API, you should
 * not need to deal with creating or managing node types manually. New node types
 * should be defined in a NodeTypes.yaml file.
 *
 * @api
 */
class NodeType extends \TYPO3\TYPO3CR\Domain\Model\NodeType {
	public function __construct($nodeType) {
		return parent::__construct($nodeType->getName(), $nodeType->getDeclaredSuperTypes(), $nodeType->getConfiguration());
	}

	public function getPackage() {
		$parts = explode(':', $this->getName());
		return $parts[0];
	}

	public function getNodeTypeOfNodeProperty($propertyName) {
		$nodeTypes = $this->getDeclaredSuperTypes();
		$nodeTypes = array_reverse($nodeTypes);
		$nodeTypes[] = $this;
		foreach ($nodeTypes as $nodeType) {
			if (array_key_exists($propertyName, $nodeType->getProperties())) {
				return $nodeType;
			}
		}
	}

	public function getSuperTypes($nodeType) {
		$superTypes = $nodeType->getDeclaredSuperTypes();
		foreach ($superTypes as $superType) {
			$superTypes = array_merge($superTypes, $this->getSuperTypes($superType));
		}
		return $superTypes;
	}
}
?>