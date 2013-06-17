<?php
namespace Flowpack\NodeTypeBuilder\Domain\Repository;


/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\NodeTypeBuilder\Domain\Model\Property;
use Flowpack\NodeTypeBuilder\Domain\Model\Type;
use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class TypeRepository {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\NodeTypeBuilder\Builder\PropertyTypeManager
	 */
	protected $propertyTypeManager;

	public function findAll() {
		$nodeTypes = $this->nodeTypeManager->getFullConfiguration();

		$types = array();
		foreach ($nodeTypes as $nodeType => $nodeTypeConfiguration) {
			$type = new Type();
			$nodeType = $this->nodeTypeManager->getNodeType($nodeType);

			$type->setId($nodeType->getName());
			$type->setLabel($nodeType->getLabel());
			if (isset($nodeTypeConfiguration['ui']['icon']['dark'])) {
				$type->setIcon('/_Resources/Static/Packages/Flowpack.NodeTypeBuilder/' . $nodeTypeConfiguration['ui']['icon']['dark']);
			}

			#var_dump($nodeTypeConfiguration);
			$property = new Property();
			$property->setId(10);
			$property->setLabel('foo');
			$property->setName('foo');
			$property->setType('foo');
			$type->setProperties(array( $property ));

			$types[] = $type;
		}

		return $types;
	}

	public function findByIdentifier($identifier) {
		$nodeTypes = $this->nodeTypeManager->getFullConfiguration();

		$type = new Type();
		$nodeType = $this->nodeTypeManager->getNodeType($identifier);

		$type->setId($nodeType->getName());
		$type->setLabel($nodeType->getLabel());
		if (isset($nodeTypes[$identifier]['ui']['icon']['dark'])) {
			$type->setIcon('/_Resources/Static/Packages/Flowpack.NodeTypeBuilder/' . $nodeTypes[$identifier]['ui']['icon']['dark']);
		}

		$property = new Property();
		$property->setId(10);
		$property->setLabel('foo');
		$type->setProperties(array( $property ));

		return $type;
	}
}
?>