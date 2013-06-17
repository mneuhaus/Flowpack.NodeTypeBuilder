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

class RestController extends \TYPO3\Flow\Mvc\Controller\ActionController {
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

	// public function init() {
	// 	$nodeTypes = $this->nodeTypeManager->getFullConfiguration();

	// 	foreach ($nodeTypes as $nodeType => $nodeTypeConfiguration) {
	// 		$nodeTypes[$nodeType] = $this->nodeTypeManager->getNodeType($nodeType);
	// 	}

	// 	$this->view->assign('nodeTypes', $nodeTypes);
	// }

	/**
	 * @param string $resource
	 * @return void
	 */
	public function findAllAction($resource) {
		header('Content-Type', 'application/json');

		switch ($resource) {
			case 'types':
					$foo = array(
						'types' => array(
							array(
								'id' => 1,
								'label' => 'Abstract Node',
								'name' => 'TYPO3.Neos.NodeTypes:AbstractNode',
								'icon' => '/_Resources/Static/Packages/Flowpack.NodeTypeBuilder/Images/box_icon-48.png',
								'properties' => array(100)
							)
						)
					);
				break;

			default:
				$foo = array();
				break;
		}
		return json_encode($foo);
	}

	/**
	 * @return void
	 */
	public function findAction() {
		header('Content-Type', 'application/json');

		$foo = array(
			'type' => array(
				'id' => 1,
				'label' => 'Abstract Node',
				'name' => 'TYPO3.Neos.NodeTypes:AbstractNode',
				'icon' => '/_Resources/Static/Packages/Flowpack.NodeTypeBuilder/Images/box_icon-48.png',
				'properties' => array(100)
			)
		);
		return json_encode($foo);
	}
}

?>