<?php
/**
 * VectorAd - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

use VectorAd\Constants;

/**
 * QuickTemplate subclass for VectorAd
 * @ingroup Skins
 * @deprecated Since 1.35, duplicate class locally if its functionality is needed.
 * Extensions or skins should extend it under no circumstances.
 */
class VectorAdTemplate extends BaseTemplate {
	/** @var array of alternate message keys for menu labels */
	private const MENU_LABEL_KEYS = [
		'cactions' => 'vectorad-more-actions',
		'tb' => 'toolbox',
		'personal' => 'personaltools',
		'lang' => 'otherlanguages',
	];
	/** @var int */
	private const MENU_TYPE_DEFAULT = 0;
	/** @var int */
	private const MENU_TYPE_TABS = 1;
	/** @var int */
	private const MENU_TYPE_DROPDOWN = 2;
	private const MENU_TYPE_PORTAL = 3;

	/**
	 * T243281: Code used to track clicks to opt-out link.
	 *
	 * The "vct" substring is used to describe the newest "VectorAd" (non-legacy)
	 * feature. The "w" describes the web platform. The "1" describes the version
	 * of the feature.
	 *
	 * @see https://wikitech.wikimedia.org/wiki/Provenance
	 * @var string
	 */
	private const OPT_OUT_LINK_TRACKING_CODE = 'vctw1';

	/** @var TemplateParser */
	private $templateParser;
	/** @var string File name of the root (master) template without folder path and extension */
	private $templateRoot;

	/** @var bool */
	private $isLegacy;

	/**
	 * @param Config $config
	 * @param TemplateParser $templateParser
	 * @param bool $isLegacy
	 */
	public function __construct(
		Config $config,
		TemplateParser $templateParser,
		bool $isLegacy
	) {
		parent::__construct( $config );

		$this->templateParser = $templateParser;
		$this->isLegacy = $isLegacy;
		$this->templateRoot = $isLegacy ? 'skin-legacy' : 'skin';
	}

	/**
	 * @return Config
	 */
	private function getConfig() {
		return $this->config;
	}

	/**
	 * The template parser might be undefined. This function will check if it set first
	 *
	 * @return TemplateParser
	 */
	protected function getTemplateParser() {
		if ( $this->templateParser === null ) {
			throw new \LogicException(
				'TemplateParser has to be set first via setTemplateParser method'
			);
		}
		return $this->templateParser;
	}

	/**
	 * @deprecated Please use Skin::getTemplateData instead
	 * @return array Returns an array of data shared between VectorAd and legacy
	 * VectorAd.
	 */
	private function getSkinData() : array {
		global $wgTopBannerCode;

		// @phan-suppress-next-line PhanUndeclaredMethod
		$contentNavigation = $this->getSkin()->getMenuProps();
		$skin = $this->getSkin();
		$out = $skin->getOutput();
		$title = $out->getTitle();

		// Naming conventions for Mustache parameters.
		//
		// Value type (first segment):
		// - Prefix "is" or "has" for boolean values.
		// - Prefix "msg-" for interface message text.
		// - Prefix "html-" for raw HTML.
		// - Prefix "data-" for an array of template parameters that should be passed directly
		//   to a template partial.
		// - Prefix "array-" for lists of any values.
		//
		// Source of value (first or second segment)
		// - Segment "page-" for data relating to the current page (e.g. Title, WikiPage, or OutputPage).
		// - Segment "hook-" for any thing generated from a hook.
		//   It should be followed by the name of the hook in hyphenated lowercase.
		//
		// Conditionally used values must use null to indicate absence (not false or '').
		$mainPageHref = Skin::makeMainPageUrl();
		// From Skin::getNewtalks(). Always returns string, cast to null if empty.
		$newTalksHtml = $skin->getNewtalks() ?: null;

		// @phan-suppress-next-line PhanUndeclaredMethod
		$commonSkinData = $skin->getTemplateData() + [
			'html-headelement' => $out->headElement( $skin ),
			'page-langcode' => $title->getPageViewLanguage()->getHtmlCode(),
			'page-isarticle' => (bool)$out->isArticle(),

			// Remember that the string '0' is a valid title.
			// From OutputPage::getPageTitle, via ::setPageTitle().
			'html-title' => $out->getPageTitle(),
			'msg-tagline' => $skin->msg( 'tagline' )->text(),

			'html-newtalk' => $newTalksHtml ? '<div class="usermessage">' . $newTalksHtml . '</div>' : '',

			'msg-vectorad-jumptonavigation' => $skin->msg( 'vectorad-jumptonavigation' )->text(),
			'msg-vectorad-jumptosearch' => $skin->msg( 'vectorad-jumptosearch' )->text(),

			'html-printfooter' => $skin->printSource(),
			'html-categories' => $skin->getCategories(),
			'data-footer' => $this->getFooterData(),
			'html-navigation-heading' => $skin->msg( 'navigation-heading' ),
			'data-search-box' => $this->buildSearchProps(),

			// Header
			'data-logos' => ResourceLoaderSkinModule::getAvailableLogos( $this->getConfig() ),
			'msg-sitetitle' => $skin->msg( 'sitetitle' )->text(),
			'msg-sitesubtitle' => $skin->msg( 'sitesubtitle' )->text(),
			'main-page-href' => $mainPageHref,

			'data-sidebar' => $this->buildSidebar(),
			'sidebar-visible' => $this->isSidebarVisible(),
			'msg-vectorad-action-toggle-sidebar' => $skin->msg( 'vectorad-action-toggle-sidebar' )->text(),

			// Advertisement
			'html-site-banner' => $this->getAdvertisementBoxOben(),
			'html-bottombanner' => $this->getAdvertisementBoxUnten(),
		] + $this->getMenuProps();

		$issetSitenoticeBox    = !empty( $commonSkinData['html-site-notice'] );
		$issetAdvertisementBox = isset( $wgTopBannerCode );
		// Randomly display either the sitenotice or the banner ad.
		// Zeige zufallsgesteuert entweder die Sitenotice oder den Werbebanner an.
		if ( $issetSitenoticeBox && $issetAdvertisementBox ) {
			if ( rand(0, 1) ) {
#				$commonSkinData['html-site-notice'] = [...]; // is yet set
				$commonSkinData['html-site-banner'] = null;
			} else {
				$commonSkinData['html-site-notice'] = null;
#				$commonSkinData['html-site-banner'] = [...]; // is yet set
			}
		} elseif ( $issetSitenoticeBox ) {
#			$commonSkinData['html-site-notice'] = [...]; // is yet set
			$commonSkinData['html-site-banner'] = null;
		} elseif ( $issetAdvertisementBox ) {
			$commonSkinData['html-site-notice'] = null;
#			$commonSkinData['html-site-banner'] = [...]; // is yet set
		}

		// The following logic is unqiue to VectorAd (not used by legacy VectorAd) and
		// is planned to be moved in a follow-up patch.
		if ( !$this->isLegacy && $skin->getUser()->isLoggedIn() ) {
			$commonSkinData['data-sidebar']['data-emphasized-sidebar-action'] = [
				'href' => SpecialPage::getTitleFor(
					'Preferences',
					false,
					'mw-prefsection-rendering-skin-skin-prefs'
				)->getLinkURL( 'wprov=' . self::OPT_OUT_LINK_TRACKING_CODE ),
				'text' => $skin->msg( 'vectorad-opt-out' )->text(),
				'title' => $skin->msg( 'vectorad-opt-out-tooltip' )->text(),
			];
		}

		return $commonSkinData;
	}

	/**
	 * Renders the entire contents of the HTML page.
	 */
	public function execute() {
		$tp = $this->getTemplateParser();
		echo $tp->processTemplate( $this->templateRoot, $this->getSkinData() );
	}

	/**
	 * Get rows that make up the footer
	 * @return array for use in Mustache template describing the footer elements.
	 */
	private function getFooterData() : array {
		$skin = $this->getSkin();
		$footerRows = [];
		foreach ( $this->getFooterLinks() as $category => $links ) {
			$items = [];
			$rowId = "footer-$category";

			foreach ( $links as $link ) {
				$items[] = [
					'id' => "$rowId-$link",
					'html' => $this->get( $link, '' ),
				];
			}

			$footerRows[] = [
				'id' => $rowId,
				'className' => null,
				'array-items' => $items
			];
		}

		// If footer icons are enabled append to the end of the rows
		$footerIcons = $this->getFooterIcons( 'icononly' );
		if ( count( $footerIcons ) > 0 ) {
			$items = [];
			foreach ( $footerIcons as $blockName => $blockIcons ) {
				$html = '';
				foreach ( $blockIcons as $icon ) {
					$html .= $skin->makeFooterIcon( $icon );
				}
				$items[] = [
					'id' => 'footer-' . htmlspecialchars( $blockName ) . 'ico',
					'html' => $html,
				];
			}

			$footerRows[] = [
				'id' => 'footer-icons',
				'className' => 'noprint',
				'array-items' => $items,
			];
		}

		ob_start();
		Hooks::run( 'VectorAdBeforeFooter', [], '1.35' );
		$htmlHookVectorAdBeforeFooter = ob_get_contents();
		ob_end_clean();

		$data = [
			'html-hook-vectorad-before-footer' => $htmlHookVectorAdBeforeFooter,
			'array-footer-rows' => $footerRows,
		];

		return $data;
	}

	/**
	 * Determines wheather the initial state of sidebar is visible on not
	 *
	 * @return bool
	 */
	private function isSidebarVisible() {
		$skin = $this->getSkin();
		if ( $skin->getUser()->isLoggedIn() ) {
			$userPrefSidebarState = $skin->getUser()->getOption(
				Constants::PREF_KEY_SIDEBAR_VISIBLE
			);

			$defaultLoggedinSidebarState = $this->getConfig()->get(
				Constants::CONFIG_KEY_DEFAULT_SIDEBAR_VISIBLE_FOR_AUTHORISED_USER
			);

			// If the sidebar user preference has been set, return that value,
			// if not, then the default sidebar state for logged-in users.
			return ( $userPrefSidebarState !== null )
				? (bool)$userPrefSidebarState
				: $defaultLoggedinSidebarState;
		}
		return $this->getConfig()->get(
			Constants::CONFIG_KEY_DEFAULT_SIDEBAR_VISIBLE_FOR_ANONYMOUS_USER
		);
	}

	/**
	 * Render a series of portals
	 *
	 * @return array
	 */
	private function buildSidebar() : array {
		global $wgAdSidebarTopCode, $wgAdSidebarBottomCode;
		global $wgAdSidebarTopType, $wgAdSidebarBottomType;

		$skin = $this->getSkin();
		$portals = $skin->buildSidebar();
		$props = [];
		$languages = null;

		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$portal = $this->getMenuData(
						'tb', $content, self::MENU_TYPE_PORTAL
					);
					// Run deprecated hook.
					// Use SidebarBeforeOutput instead.
					ob_start();
					Hooks::run( 'VectorAdAfterToolbox', [], '1.35' );
					$props[] = $portal + [
						'html-hook-vector-after-toolbox' => ob_get_clean(),
					];
					break;
				case 'AD1':
					if ( !empty( $wgAdSidebarTopCode ) ) {
						$tmp_name = isset($wgAdSidebarTopType) ? $wgAdSidebarTopType : 'advertising';
						$props[] = $this->getWimaData( $tmp_name, $wgAdSidebarTopCode );
					}
					break;
				case 'AD2':
					if ( !empty( $wgAdSidebarBottomCode ) ) {
						$tmp_name = isset($wgAdSidebarBottomType) ? $wgAdSidebarBottomType : 'advertising';
						$props[] = $this->getWimaData( $tmp_name, $wgAdSidebarBottomCode );
					}
					break;
				case 'LANGUAGES':
					$portal = $this->getMenuData(
						'lang',
						$content,
						self::MENU_TYPE_PORTAL
					);
					// The language portal will be added provided either
					// languages exist or there is a value in html-after-portal
					// for example to show the add language wikidata link (T252800)
					if ( count( $content ) || $portal['html-after-portal'] ) {
						$languages = $portal;
					}
					break;
				default:
					// Historically some portals have been defined using HTML rather than arrays.
					// Let's move away from that to an uniform definition.
					if ( !is_array( $content ) ) {
						$html = $content;
						$content = [];
						wfDeprecated(
							"`content` field in portal $name must be array."
								. "Previously it could be a string but this is no longer supported.",
							'1.35.0'
						);
					} else {
						$html = false;
					}
					$portal = $this->getMenuData(
						$name, $content, self::MENU_TYPE_PORTAL
					);
					if ( $html ) {
						$portal['html-items'] .= $html;
					}
					$props[] = $portal;
					break;
			}
		}

		if ( !empty( $this->getDonationBox() ) ) {
			$props[] = $this->getDonationBox();
		}
		if ( !empty( $this->getFacebookBox() ) ) {
			$props[] = $this->getFacebookBox();
		}
		if ( !empty( $this->getAltersklassifizierungBox() ) ) {
			$props[] = $this->getAltersklassifizierungBox();
		}

		$firstPortal = $props[0] ?? null;
		if ( $firstPortal ) {
			$firstPortal[ 'class' ] .= ' portal-first';
		}

		return [
			'has-logo' => $this->isLegacy,
			'html-logo-attributes' => Xml::expandAttributes(
				Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) + [
					'class' => 'mw-wiki-logo',
					'href' => Skin::makeMainPageUrl(),
				]
			),
			'array-portals-rest' => array_slice( $props, 1 ),
			'data-portals-first' => $firstPortal,
			'data-portals-languages' => $languages,
		];
	}

	/**
	 * @param string $label to be used to derive the id and human readable label of the menu
	 *  If the key has an entry in the constant MENU_LABEL_KEYS then that message will be used for the
	 *  human readable text instead.
	 * @param array $urls to convert to list items stored as string in html-items key
	 * @param int $type of menu (optional) - a plain list (MENU_TYPE_DEFAULT),
	 *   a tab (MENU_TYPE_TABS) or a dropdown (MENU_TYPE_DROPDOWN)
	 * @param array $options (optional) to be passed to makeListItem
	 * @param bool $setLabelToSelected (optional) the menu label will take the value of the
	 *  selected item if found.
	 * @return array
	 */
	private function getMenuData(
		string $label,
		array $urls = [],
		int $type = self::MENU_TYPE_DEFAULT,
		array $options = [],
		bool $setLabelToSelected = false
	) : array {
		$skin = $this->getSkin();
		$extraClasses = [
			self::MENU_TYPE_DROPDOWN => 'vectorad-menu vectorad-menu-dropdown vectoradMenu',
			self::MENU_TYPE_TABS => 'vectorad-menu vectorad-menu-tabs vectoradTabs',
			self::MENU_TYPE_PORTAL => 'vectorad-menu vectorad-menu-portal portal',
			self::MENU_TYPE_DEFAULT => 'vectorad-menu',
		];
		// A list of classes to apply the list element and override the default behavior.
		$listClasses = [
			// `.menu` is on the portal for historic reasons.
			// It should not be applied elsewhere per T253329.
			self::MENU_TYPE_DROPDOWN => 'menu vectorad-menu-content-list',
		];
		$isPortal = self::MENU_TYPE_PORTAL === $type;

		// For some menu items, there is no language key corresponding with its menu key.
		// These inconsitencies are captured in MENU_LABEL_KEYS
		$msgObj = $skin->msg( self::MENU_LABEL_KEYS[ $label ] ?? $label );
		$props = [
			'id' => "p-$label",
			'label-id' => "p-{$label}-label",
			// If no message exists fallback to plain text (T252727)
			'label' => $msgObj->exists() ? $msgObj->text() : $label,
			'list-classes' => $listClasses[$type] ?? 'vectorad-menu-content-list',
			'html-items' => '',
			'is-dropdown' => self::MENU_TYPE_DROPDOWN === $type,
			'html-tooltip' => Linker::tooltip( 'p-' . $label ),
		];

		foreach ( $urls as $key => $item ) {
			// Add CSS class 'collapsible' to all links EXCEPT watchstar.
			if (
				$key !== 'watch' && $key !== 'unwatch' &&
				isset( $options['vectorad-collapsible'] ) && $options['vectorad-collapsible'] ) {
				if ( !isset( $item['class'] ) ) {
					$item['class'] = '';
				}
				$item['class'] = rtrim( 'collapsible ' . $item['class'], ' ' );
			}
			$props['html-items'] .= $this->getSkin()->makeListItem( $key, $item, $options );

			// Check the class of the item for a `selected` class and if so, propagate the items
			// label to the main label.
			if ( $setLabelToSelected ) {
				if ( isset( $item['class'] ) && stripos( $item['class'], 'selected' ) !== false ) {
					$props['label'] = $item['text'];
				}
			}
		}

		$props['html-after-portal'] = $isPortal ? $this->getAfterPortlet( $label ) : '';

		// Mark the portal as empty if it has no content
		$class = ( count( $urls ) == 0 && !$props['html-after-portal'] )
			? 'vectorad-menu-empty emptyPortlet' : '';
		$props['class'] = trim( "$class $extraClasses[$type]" );
		return $props;
	}

	/**
	 * Corresponds to function getMenuData for the ad blocks.
	 * @return array
	 */
	private function getWimaData(
		string $label,
		string $content
	) : array {

		$body_class = 'vectorad-sidebar-advertising';
		switch ( $label ) {
			case 'facebook':
				$body_class = 'vectorad-sidebar-button';
				break;
			case 'donations':
				$body_class = 'vectorad-sidebar-button';
				break;
			case 'labelled':
				$body_class = 'vectorad-sidebar-button';
				break;
			default:
				$body_class = 'vectorad-sidebar-advertising';
				break;
		}
		if ( $label === 'advertising' )  $body_class = 'vectorad-sidebar-advertising';

		$content = '<li>' . $content . '</li>';
		$props = [
			'class' => $body_class,
			'id' => "p-advertising",
			'label-id' => "p-{$label}-label",
			'label' => wfMessage( $label ),
			'list-classes' => 'vectorad-menu vectorad-menu-portal portal',
			'html-items' => $content,
			'is-dropdown' => false,
			'html-tooltip' => Linker::tooltip( 'p-' . $label )
		];

		return $props;
	}

	/**
	 * @return array
	 */
	private function getMenuProps() : array {
		// @phan-suppress-next-line PhanUndeclaredMethod
		$contentNavigation = $this->getSkin()->getMenuProps();
		$personalTools = $this->getPersonalTools();
		$skin = $this->getSkin();

		// For logged out users VectorAd shows a "Not logged in message"
		// This should be upstreamed to core, with instructions for how to hide it for skins
		// that do not want it.
		// For now we create a dedicated list item to avoid having to sync the API internals
		// of makeListItem.
		if ( !$skin->getUser()->isLoggedIn() && User::groupHasPermission( '*', 'edit' ) ) {
			$loggedIn =
				Html::element( 'li',
					[ 'id' => 'pt-anonuserpage' ],
					$skin->msg( 'notloggedin' )->text()
				);
		} else {
			$loggedIn = '';
		}

		// This code doesn't belong here, it belongs in the UniversalLanguageSelector
		// It is here to workaround the fact that it wants to be the first item in the personal menus.
		if ( array_key_exists( 'uls', $personalTools ) ) {
			$uls = $skin->makeListItem( 'uls', $personalTools[ 'uls' ] );
			unset( $personalTools[ 'uls' ] );
		} else {
			$uls = '';
		}

		$ptools = $this->getMenuData( 'personal', $personalTools );
		// Append additional link items if present.
		$ptools['html-items'] = $uls . $loggedIn . $ptools['html-items'];

		return [
			'data-personal-menu' => $ptools,
			'data-namespace-tabs' => $this->getMenuData(
				'namespaces',
				$contentNavigation[ 'namespaces' ] ?? [],
				self::MENU_TYPE_TABS
			),
			'data-variants' => $this->getMenuData(
				'variants',
				$contentNavigation[ 'variants' ] ?? [],
				self::MENU_TYPE_DROPDOWN,
				[], true
			),
			'data-page-actions' => $this->getMenuData(
				'views',
				$contentNavigation[ 'views' ] ?? [],
				self::MENU_TYPE_TABS, [
					'vectorad-collapsible' => true,
				]
			),
			'data-page-actions-more' => $this->getMenuData(
				'cactions',
				$contentNavigation[ 'actions' ] ?? [],
				self::MENU_TYPE_DROPDOWN
			),
		];
	}

	/**
	 * @return array
	 */
	private function buildSearchProps() : array {
		$config = $this->getConfig();
		$skin = $this->getSkin();
		$props = [
			'form-action' => $config->get( 'Script' ),
			'html-button-search-fallback' => $this->makeSearchButton(
				'fulltext',
				[ 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ]
			),
			'html-button-search' => $this->makeSearchButton(
				'go',
				[ 'id' => 'searchButton', 'class' => 'searchButton' ]
			),
			'html-input' => $this->makeSearchInput( [ 'id' => 'searchInput' ] ),
			'msg-search' => $skin->msg( 'search' ),
			'page-title' => SpecialPage::getTitleFor( 'Search' )->getPrefixedDBkey(),
		];
		return $props;
	}

	/**
	 * Renderer for advertisement block
	 *
	 * @return string html
	 */
	private function getAdvertisementBoxOben() {
		global $wgTopBannerCode, $wgTopBannerStyle, $wgTopBannerType;
		$id    = 'siteNotice';
		$class = 'body';
		$style = $wgTopBannerStyle;

		return $this->getAdvertisementBox( $wgTopBannerCode, $wgTopBannerType, $id, $class, $style );
	}
	private function getAdvertisementBoxUnten() {
		global $wgBottomBannerCode, $wgBottomBannerStyle, $wgBottomBannerType;
		$id    = 'bottomBanner';
		$class = 'body';
		$style = $wgBottomBannerStyle;

		return $this->getAdvertisementBox( $wgBottomBannerCode, $wgBottomBannerType, $id, $class, $style );
	}
	private function getAdvertisementBox( $code, $type, $id, $class, $style ) {

		if ( isset( $code ) ) {
			$msg_key = isset($type) ? $type : 'advertising';
			return $this->getMsg( 'vectorad-' . $msg_key ) . ':' .
				'<div class="' . $class .
				( isset($style) ? '" style="' . $style : '' ) .
			    '">' .
			    $code .
			    '</div>';
		}

		return '';
	}

	/**
	 * Renderer for donation block
	 *
	 * @return string html
	 */
	private function getDonationBox() {
		global $wgDonationButton, $wgDonationButtonIMG, $wgDonationButtonURL;
		global $wgLanguageCode;
		if ( empty($wgDonationButton) ) return ''; // Do nothing
		if ( empty($wgDonationButtonIMG) ) return ''; // Do nothing
		if ( empty($wgDonationButtonURL) ) return ''; // Do nothing
		$html = '';

		if (($wgDonationButton === 'true') || ($wgDonationButton === true)) {
			// If the passed URL ends with a '=', append the language abbreviation to make the donation page language sensitive.
			// Wenn die übergebene URL mit einem '=' endet, das Sprachenkürzel anhängen, um die Spendenseite sprachsensitiv zu behandeln.
			if (substr ( $wgDonationButtonURL, (strlen ( $wgDonationButtonURL ) - 1), 1 ) === '=') {
				$wgDonationButtonURL .= ((strlen ( wfMessage( 'lang' ) ) == 2) ? wfMessage( 'lang' ) : $wgLanguageCode);
			}
			if (substr ( $wgDonationButtonIMG, 0, 1 ) !== '/') {
				$wgDonationButtonIMG = '/' . $wgDonationButtonIMG;
			}
			// If the domin contains a subdomain, try adjusting the subdomain of the language selection to select the button image language sensitively.
			// Wenn die Domin eine Subdomain enthält, versuche die Subdomain der Sprachauswahl anzupassen, um das Button-Bild sprachsensitiv auszuwählen.
			$tmpServerDomain = substr ( $wgDonationButtonIMG, strpos ( $wgDonationButtonIMG, '//' )+2);
			$tmpServerDomain = substr ( $tmpServerDomain, 0, strpos ( $tmpServerDomain, '/' ));
			$tmpDonationButtonIMG = substr ( $wgDonationButtonIMG, strpos ( $wgDonationButtonIMG, $tmpServerDomain ) + strlen ( $tmpServerDomain ));
			if (substr_count ( $tmpServerDomain, '.' ) == 2) {
				$tmpLang = substr ( $tmpServerDomain, 0, strpos ( $tmpServerDomain, '.' ));
				if (strlen ( wfMessage( 'lang' ) ) == 2) {
					$tmpServerDomain = wfMessage( 'lang' ) . substr ( $tmpServerDomain, strpos ( $tmpServerDomain, '.' ));
				}
			}
			$html = '<a href="//' . $wgDonationButtonURL . '"><img alt="Donate-Button" src="//' . $tmpServerDomain . $tmpDonationButtonIMG . '" style="height:26px; width:92px;" /></a>';
		}

		return $this->getWimaData( 'donations', $html );
	}

	/**
	 * Renderer for wima block
	 *
	 * @return string html
	 */
	private function getWimaBox( $buttonActive, $buttonIMG, $buttonURL, $buttonAlt, $buttonStyle ) {

		if ( empty($buttonActive) ) return ''; // Do nothing
		if ( empty($buttonIMG) ) return ''; // Do nothing
		if ( empty($buttonURL) ) return ''; // Do nothing
		$html = '';

		if (($buttonActive === 'true') || ($buttonActive === true)) {
			$html = '<div title="Reiner Link ohne Skripte" class="body" style="margin-left:-5px;text-align:center;">';
			$html .= '<a href="//' . $buttonURL . '"><img alt="'.$buttonAlt.'" src="//' . $buttonIMG . '" style="'.$buttonStyle.'" /></a>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Renderer for facebook block
	 *
	 * @return string html
	 */
	private function getFacebookBox() {
		global $wgFacebookButton, $wgFacebookButtonIMG, $wgFacebookButtonURL;

		return $this->getWimaData( 'facebook', $this->getWimaBox( $wgFacebookButton, $wgFacebookButtonIMG, $wgFacebookButtonURL, 'Facebook-Button', 'width:148px; height:57px;' ) );
	}

	/**
	 * Renderer for Altersklassifizierung block
	 *
	 * @return string html
	 */
	private function getAltersklassifizierungBox() {
		global $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL;

		return $this->getWimaData( 'labelled', $this->getWimaBox( $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL, 'AgeClassification-Button', 'width:148px; height:28px;' ) );
	}
}
