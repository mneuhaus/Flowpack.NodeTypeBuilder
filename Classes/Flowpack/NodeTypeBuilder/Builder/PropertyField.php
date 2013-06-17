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

use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class PropertyField {

	/**
	 *
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var array
	 */
	protected $configuration;

	public function __construct($name, $configuration) {
		$this->name = $name;
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

	public function getFormElementType() {
		return $this->configuration['type'];
	}

	public function getLabel() {
		return $this->configuration['ui']['label'];
	}

	public function getValuePath() {
		return $this->configuration['valuePath'];
	}

	public function getProperties() {
		if (!isset($this->configuration['ui']['properties'])) {
			return array();
		}
		return $this->configuration['ui']['properties'];
	}
}
?>