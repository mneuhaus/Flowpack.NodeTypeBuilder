<?php
namespace Flowpack\NodeTypeBuilder\Domain\Model;


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
class Type {
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $icon;

	/**
	 * @var array<\Flowpack\NodeTypeBuilder\Domain\Model\Type>
	 */
	protected $properties = array();

	/**
	 * @param string $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param array $properties
	 */
	public function setProperties($properties) {
		$this->properties = $properties;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}
}

?>