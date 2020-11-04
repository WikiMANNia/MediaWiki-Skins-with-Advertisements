<?php
namespace MediaWiki\Skins\VectorAd\Tests\Integration;

use GlobalVarConfig;
use MediaWikiIntegrationTestCase;
use RequestContext;
use TemplateParser;
use Title;
use VectorAdTemplate;
use Wikimedia\TestingAccessWrapper;

/**
 * Class VectorAdTemplateTest
 * @package MediaWiki\Skins\VectorAd\Tests\Unit
 * @group VectorAd
 * @group Skins
 *
 * @coversDefaultClass \VectorAdTemplate
 */
class VectorAdTemplateTest extends MediaWikiIntegrationTestCase {

	/**
	 * @return \VectorAdTemplate
	 */
	private function provideVectorAdTemplateObject() {
		$template = new VectorAdTemplate(
			GlobalVarConfig::newInstance(),
			new TemplateParser(),
			true
		);
		$template->set( 'skin', new \SkinVectorAd() );
		return $template;
	}

	/**
	 * @param string $nodeString an HTML of the node we want to verify
	 * @param string $tag Tag of the element we want to check
	 * @param string $attribute Attribute of the element we want to check
	 * @param string $search Value of the attribute we want to verify
	 * @return bool
	 */
	private function expectNodeAttribute( $nodeString, $tag, $attribute, $search ) {
		$node = new \DOMDocument();
		$node->loadHTML( $nodeString );
		$element = $node->getElementsByTagName( $tag )->item( 0 );
		if ( !$element ) {
			return false;
		}

		$values = explode( ' ', $element->getAttribute( $attribute ) );
		return in_array( $search, $values );
	}

	/**
	 * @covers ::getMenuData
	 */
	public function testMakeListItemRespectsCollapsibleOption() {
		$vectoradTemplate = $this->provideVectorAdTemplateObject();
		$template = TestingAccessWrapper::newFromObject( $vectoradTemplate );
		$listItemClass = 'my_test_class';
		$options = [ 'vectorad-collapsible' => true ];
		$item = [ 'class' => $listItemClass ];
		$propsCollapsible = $template->getMenuData(
			'foo',
			[
				'bar' => $item,
			],
			0,
			$options
		);
		$propsNonCollapsible = $template->getMenuData(
			'foo',
			[
				'bar' => $item,
			],
			0,
			[]
		);
		$nonCollapsible = $propsNonCollapsible['html-items'];
		$collapsible = $propsCollapsible['html-items'];

		$this->assertTrue(
			$this->expectNodeAttribute( $collapsible, 'li', 'class', 'collapsible' ),
			'The collapsible element has to have `collapsible` class'
		);
		$this->assertFalse(
			$this->expectNodeAttribute( $nonCollapsible, 'li', 'class', 'collapsible' ),
			'The non-collapsible element should not have `collapsible` class'
		);
		$this->assertTrue(
			$this->expectNodeAttribute( $nonCollapsible, 'li', 'class', $listItemClass ),
			'The non-collapsible element should preserve item class'
		);
	}

	/**
	 * @covers ::getMenuProps
	 */
	public function testGetMenuProps() {
		$title = Title::newFromText( 'SkinTemplateVectorAd' );
		$context = RequestContext::getMain();
		$context->setTitle( $title );
		$context->setLanguage( 'fr' );
		$vectoradTemplate = $this->provideVectorAdTemplateObject();
		// used internally by getPersonalTools
		$vectoradTemplate->set( 'personal_urls', [] );
		$this->setMwGlobals( 'wgHooks', [
			'SkinTemplateNavigation' => [
				function ( &$skinTemplate, &$content_navigation ) {
					$content_navigation = [
						'actions' => [],
						'namespaces' => [],
						'variants' => [],
						'views' => [],
					];
				}
			]
		] );
		$openVectorAdTemplate = TestingAccessWrapper::newFromObject( $vectoradTemplate );

		$props = $openVectorAdTemplate->getMenuProps();
		$views = $props['data-page-actions'];
		$namespaces = $props['data-namespace-tabs'];

		$this->assertSame( $views, [
			'id' => 'p-views',
			'label-id' => 'p-views-label',
			'label' => $context->msg( 'views' )->text(),
			'list-classes' => 'vectorad-menu-content-list',
			'html-items' => '',
			'is-dropdown' => false,
			'html-tooltip' => '',
			'html-after-portal' => '',
			'class' => 'vectorad-menu-empty emptyPortlet vectorad-menu vectorad-menu-tabs vectoradTabs',
		] );

		$variants = $props['data-variants'];
		$actions = $props['data-page-actions-more'];
		$this->assertSame( $namespaces['class'],
			'vectorad-menu-empty emptyPortlet vectorad-menu vectorad-menu-tabs vectoradTabs' );
		$this->assertSame( $variants['class'],
			'vectorad-menu-empty emptyPortlet vectorad-menu vectorad-menu-dropdown vectoradMenu' );
		$this->assertSame( $actions['class'],
			'vectorad-menu-empty emptyPortlet vectorad-menu vectorad-menu-dropdown vectoradMenu' );
		$this->assertSame( $props['data-personal-menu']['class'],
			'vectorad-menu-empty emptyPortlet vectorad-menu' );
	}

}
