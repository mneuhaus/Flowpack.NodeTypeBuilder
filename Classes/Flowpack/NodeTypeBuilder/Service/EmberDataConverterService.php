<?php
namespace Flowpack\NodeTypeBuilder\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Radmiraal\Emberjs\Utility\EmberDataUtility;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Utility\TypeHandling;

/**
 * An Ember Data View
 *
 * This view conforms to the DS.RESTAdapter conventions of Ember JS:
 *
 * - Underscoring attributes
 * - Relationships
 * - Sideloading:
 *
 * @see http://emberjs.com/guides/models/the-rest-adapter/#toc_underscored-attribute-names
 * @see http://emberjs.com/guides/models/the-rest-adapter/#toc_relationships
 * @see http://emberjs.com/guides/models/the-rest-adapter/#toc_sideloaded-relationships
 */
class EmberDataConverterService  {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var array
	 */
	protected $settings = array();

	public function __construct(\TYPO3\Flow\Configuration\ConfigurationManager $configurationManager) {
		$this->settings = $configurationManager->getConfiguration('EmberData');
	}

	public function convertToEmberData($source) {
		$name = $this->getName($this->getClassName($source), is_array($source));
		$output = array($name => array());
		$output[$name] = $this->transformValue($source, $output);

		return $output;
	}

	public function getName($className, $plural = FALSE) {
		if (!isset($this->settings[$className])) {
			return 'objects';
		}

		if ($plural === TRUE && isset($this->settings[$className]['plural'])) {
			return $this->settings[$className]['plural'];
		}

		if ($plural === TRUE) {
			return $this->settings[$className]['name'] . 's';
		}

		return $this->settings[$className]['name'];
	}

	public function transformValue($source, &$sideload = array(), $configuration = array()) {
		if (is_array($source) || $source instanceof \ArrayAccess) {
			return $this->transformArray($source, $sideload, $configuration);
		} elseif (is_object($source)) {
			return $this->transformObject($source, $sideload, $configuration);
		} else {
			return $source;
		}
	}

	protected function transformArray($source, &$sideload = array(), $configuration = array()) {
		$array = array();
		foreach ($source as $key => $value) {
			if (is_object($value)) {
				$array[$key] = $this->transformObject($value, $sideload, $configuration);
			} else {
				$array[$key] = $this->transformValue($value, $sideload, $configuration);
			}
		}
		return $array;
	}

	protected function transformObject($object, &$sideload = array(), $configuration = array()) {
		if ($object instanceof \DateTime) {
			return $object->format('Y-m-d\TH:i:s');
		}

		if (isset($configuration['onlyIdentifier']) && $configuration['onlyIdentifier']) {
			$objectIdentifier = $this->persistenceManager->getIdentifierByObject($object);
			if ($objectIdentifier === NULL && method_exists($object, 'getId')) {
				$objectIdentifier = $object->getId();
			}
			return $objectIdentifier;
		}

		$transformedObject = array();

		$propertyNames = ObjectAccess::getGettablePropertyNames($object);
		foreach ($propertyNames as $propertyName) {
			if (substr($propertyName, 0, 1) !== '_') {
				$propertyConfiguration = $this->getPropertyConfiguration($object, $propertyName);
				$propertyValue = ObjectAccess::getProperty($object, $propertyName);

				if (isset($propertyConfiguration['name'])) {
					$targetPropertyName = $propertyConfiguration['name'];
				} else {
					$targetPropertyName = EmberDataUtility::uncamelize($propertyName);
				}
				$transformedObject[$targetPropertyName] = $this->transformValue($propertyValue, $sideload, $propertyConfiguration);

				if (isset($propertyConfiguration['sideload']) && $propertyConfiguration['sideload']) {
					$propertyClass = $this->getName($this->getClassName($propertyValue), TRUE);

					if ($propertyClass !== FALSE) {
						if (!isset($sideload[$propertyClass])) {
							$sideload[$propertyClass] = array();
						}
						$sideloadedData = $this->transformValue($propertyValue, $sideload);
						if (is_array($sideloadedData)) {
							$sideload[$propertyClass] = array_merge($sideload[$propertyClass], $sideloadedData);
						} else {
							$sideload[$propertyClass][] = $sideloadedData;
						}
					}
				}
			}
		}

		return $transformedObject;
	}

	public function getClassName($mixed) {
		if (is_array($mixed)) {
			$subValue = current($mixed);
			if (is_object($subValue)) {
				return get_class($subValue);
			}
		}

		if (is_object($mixed)) {
			return get_class($mixed);
		}

		return FALSE;
	}

	public function getPropertyConfiguration($object, $propertyName) {
		$className = get_class($object);

		if (!isset($this->settings[$className])) {
			return array();
		}

		if (!isset($this->settings[$className]['propertyMapping'][$propertyName])) {
			return array();
		}

		return $this->settings[$className]['propertyMapping'][$propertyName];
	}

}

?>