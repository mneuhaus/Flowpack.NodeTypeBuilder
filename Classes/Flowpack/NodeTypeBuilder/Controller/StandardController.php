<?php
namespace Flowpack\NodeTypeBuilder\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.NodeTypeBuilder".*
 *                                                                        *
 *                                                                        */

use Flowpack\NodeTypeBuilder\Builder\FormFactory;
use Flowpack\NodeTypeBuilder\Domain\Property;
use Symfony\Component\Yaml\Yaml;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Utility\Arrays;

class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {
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
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManager
	 */
	protected $packageManager;

	public function init() {
		$nodeTypes = $this->nodeTypeManager->getFullConfiguration();

		foreach ($nodeTypes as $nodeType => $nodeTypeConfiguration) {
			$nodeTypes[$nodeType] = $this->nodeTypeManager->getNodeType($nodeType);
		}

		$this->view->assign('nodeTypes', $nodeTypes);
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->init();
	}

	/**
	 * Edit a NodeType
	 * @param string $nodeType
	 * @return void
	 */
	public function editAction($nodeType) {
		$this->init();
		$nodeType = $this->nodeTypeManager->getNodeType($nodeType);
		$this->view->assign('currentNodeType', $nodeType);
	}

	/**
	 * Edit a NodeType
	 * @param array $values
	 * @return void
	 */
	public function updateAction($values) {
		$nodeTypes = $this->nodeTypeManager->getFullConfiguration();
		$nodeType = $nodeTypes[$values['nodeType']];

		$changedNodeTypes = array();
		$newNodeType = $nodeType;
		unset($newNodeType['properties']);
		foreach ($values['properties'] as $property => $fields) {
			if ($fields['type'] === NULL) {
				continue;
			}
			$propertyType = $this->propertyTypeManager->getPropertyType($fields['type']);

			if (!empty($fields['oldName']) && $fields['oldName'] !== $property && isset($nodeType['properties'][$fields['oldName']])) {
				$key = array_search($fields['oldName'], array_keys($nodeType['properties']));
				$before = array_slice($nodeType['properties'], 0, $key, TRUE);
				$after = array_slice($nodeType['properties'], $key + 1, NULL, TRUE);
				$newNodeType['properties'] = array_merge(array(), $before, array($property => $nodeType['properties'][$fields['oldName']]), $after);
			}
			unset($fields['oldName'], $fields['name']);

			foreach ($fields as $field => $value) {
				$propertyField = $propertyType->getPropertyField($field);
				if ($value == 'none') {
					continue;
				}
				$newNodeType = Arrays::setValueByPath($newNodeType, 'properties.' . $property . '.' . $propertyField->getValuePath(), $value);
			}
		}
		$changedNodeTypes[$values['nodeType']] = $newNodeType;

		$parts = explode(':', $values['nodeType']);
		$package = $parts[0];
		$packagePath = FLOW_PATH_PACKAGES . 'Application/' . $package . '/';
		$packageConfiguration = file_get_contents($packagePath . '/Configuration/NodeTypes.yaml');
		$packageNodeTypes = Yaml::parse($packageConfiguration);


		foreach ($changedNodeTypes as $nodeTypeName => $nodeType) {
			$packageNodeTypes[$nodeTypeName] = $nodeType;
		}

		$yaml = Yaml::dump($packageNodeTypes, 10, 2);
		file_put_contents($packagePath . '/Configuration/NodeTypes.yaml', $yaml);

	}

	public function setPropertyPath($subject, $propertyPath, $value) {
		$propertyPathSegments = explode('.', $propertyPath);
		$worker = &$subject;
		foreach ($propertyPathSegments as $pathSegment) {
			$propertyExists = FALSE;
			if (is_array($worker[$pathSegment])) {
				$worker = &$worker[$pathSegment];
			} else {
				$worker[$pathSegment] = $value;
			}
		}
		return $subject;
	}
}

?>