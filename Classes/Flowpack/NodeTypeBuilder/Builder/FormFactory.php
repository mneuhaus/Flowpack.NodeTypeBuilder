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

use Flowpack\NodeTypeBuilder\Builder\ControllerCallbackFinisher;
use Flowpack\NodeTypeBuilder\Domain\NodeType;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Form\Core\Model\FormDefinition;

class FormFactory extends \TYPO3\Form\Factory\AbstractFormFactory {
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

	/**
	 * @param array $factorySpecificConfiguration
	 * @param string $presetName
	 * @return \TYPO3\Form\Core\Model\FormDefinition
	 */
	public function build(array $factorySpecificConfiguration, $presetName) {
		$formConfiguration = $this->getPresetConfiguration($presetName);
		$form = new FormDefinition('builder', $formConfiguration);

		$this->nodeType = $this->nodeTypeManager->getNodeType($factorySpecificConfiguration['nodeType']);
		$this->nodeType = new NodeType($this->nodeType);
		$this->nodeTypeProperties = $this->nodeType->getProperties();

		$mainPage = $form->createPage('main');

		$label = $mainPage->createElement('nodeLabel', 'TYPO3.Form:SingleLineText');
		$label->setLabel('NodeTye Label');
		$label->setDefaultValue($this->nodeType->getLabel());
		$type = $mainPage->createElement('nodeType', 'TYPO3.Form:SingleLineText');
		$type->setLabel('NodeType');
		$type->setDefaultValue($factorySpecificConfiguration['nodeType']);

		$fieldSection = $mainPage->createElement('properties', 'Flowpack.NodeTypeBuilder:Fields');

		foreach ($this->nodeType->getProperties() as $propertyName => $propertyConfiguration) {
			$this->addNodePropertySection($fieldSection, $propertyName, $propertyConfiguration);
		}

		$this->addTemplate($fieldSection);


		$finisher = new ControllerCallbackFinisher();
		$finisher->setOption('callbackAction', 'update');
		$form->addFinisher($finisher);

		return $form;
	}

	public function addNodePropertySection($parentElement, $propertyName, $propertyConfiguration) {
		$propertySection = $parentElement->createElement($parentElement->getIdentifier() . '.' . $propertyName, 'Flowpack.NodeTypeBuilder:Wrapper');
		$propertyType = $this->propertyTypeManager->getPropertyType($propertyConfiguration['type']);
		$propertySection->setProperty('label', $propertyConfiguration['ui']['label']);
		$propertySection->setProperty('name', $propertyName);
		$propertySection->setProperty('propertyType', $propertyType);
		$propertySection->setProperty('nodeType', $this->nodeType->getNodeTypeOfNodeProperty($propertyName));
		$propertySection->setProperty('propertyBelongsToSuperNodeType', $this->nodeType !== $this->nodeType->getNodeTypeOfNodeProperty($propertyName));
		$propertyConfiguration = $this->nodeTypeProperties[$propertyName];

		foreach ($propertyType->getPropertyFields() as $propertyField) {
			$this->addPropertyField($propertySection, $propertyField, $propertyName, $propertyConfiguration);
		}
	}

	public function addPropertyField($parentElement, $propertyField, $propertyName, $propertyConfiguration = array()) {
		$field = $parentElement->createElement($parentElement->getIdentifier() . '.' . $propertyField->getName(), $propertyField->getFormElementType());
		$field->setLabel($propertyField->getLabel());
		$field->setProperty('field', $propertyField->getName());
		foreach ($propertyField->getProperties() as $key => $value) {
			$field->setProperty($key, $value);
		}
		if ($propertyField->getValuePath() === '_key') {
			$value = $propertyName;
		} else {
			$value = ObjectAccess::getPropertyPath($propertyConfiguration, $propertyField->getValuePath());
		}
		$field->setDefaultValue($value);
	}

	public function addTemplate($parentElement) {
		foreach ($this->propertyTypeManager->getPropertyTypes() as $propertyType) {
			if (stristr($propertyType->getName(), 'Abstract')) {
				continue;
			}
			$templateSection = $parentElement->createElement('template.' . $propertyType->getName(), 'Flowpack.NodeTypeBuilder:Wrapper');
			$templateSection->setProperty('label', $propertyType->getLabel());
			$templateSection->setProperty('name', $propertyType->getName());
			$templateSection->setProperty('propertyType', $propertyType);
			$templateSection->setProperty('class', ' field-template');

			foreach ($propertyType->getPropertyFields() as $propertyField) {
				$this->addPropertyField($templateSection, $propertyField, $propertyType->getName());
			}
		}
	}
}
?>