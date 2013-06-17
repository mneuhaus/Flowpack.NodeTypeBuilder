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

use Flowpack\NodeTypeBuilder\Builder\PropertyField;
use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class PropertyType {

	/**
	 *
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var array
	 */
	protected $superTypes = array();

	/**
	 *
	 * @var array
	 */
	protected $configuration = array();

	public function __construct($name, $superTypes, $configuration) {
		$this->name = $name;
		$this->superTypes = $superTypes;
		$this->configuration = $configuration;
	}

	/**
	 *
	 * @return string
	 * @api
	 */
	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->configuration['ui']['label'];
	}

	public function getPropertyFields() {
		$propertyFields = array();
		foreach ($this->configuration['properties'] as $propertyField => $propertyFieldConfiguration) {
			$propertyFields[$propertyField] = new PropertyField($propertyField, $propertyFieldConfiguration);
		}
		return $propertyFields;
	}

	public function getPropertyField($propertyFieldName) {
		$propertyFields = $this->getPropertyFields();
		return $propertyFields[$propertyFieldName];
	}

	public function getConfiguration() {
		return $this->configuration;
	}
}
?>