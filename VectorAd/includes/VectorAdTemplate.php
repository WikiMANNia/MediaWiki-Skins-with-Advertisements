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

/**
 * QuickTemplate subclass for VectorAd
 * @ingroup Skins
 */
class VectorAdTemplate extends BaseTemplate {

	/**
	 * Outputs the entire contents of the HTML page
	 */
	public function execute() {
		global $wgTopBannerCode;
		$issetSitenoticeBox    = $this->data['sitenotice'];
		$issetAdvertisementBox = isset($wgTopBannerCode);

		// Randomly display either the sitenotice or the banner ad.
		// Zeige zufallsgesteuert entweder die Sitenotice oder den Werbebanner an.
		if ($issetSitenoticeBox && $issetAdvertisementBox) {
			if ( rand(0, 1) ) {
#				$this->data['sitenotice'] = [...]; // is yet set
				$this->data['sitebanner'] = null;
			} else {
				$this->data['sitenotice'] = null;
				$this->data['sitebanner'] = $this->getAdvertisementBoxOben();
			}
		} elseif ($issetSitenoticeBox) {
#			$this->data['sitenotice'] = [...]; // is yet set
			$this->data['sitebanner'] = null;
		} elseif ($issetAdvertisementBox) {
			$this->data['sitenotice'] = null;
			$this->data['sitebanner'] = $this->getAdvertisementBoxOben();
		}
		$this->data['bottombanner'] = $this->getAdvertisementBoxUnten();

		$this->data['namespace_urls'] = $this->data['content_navigation']['namespaces'];
		$this->data['view_urls']      = $this->data['content_navigation']['views'];
		$this->data['action_urls']    = $this->data['content_navigation']['actions'];
		$this->data['variant_urls']   = $this->data['content_navigation']['variants'];

		// Move the watch/unwatch star outside of the collapsed "actions" menu to the main "views" menu
		if ( $this->config->get( 'VectorAdUseIconWatch' ) ) {
			$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() )
				? 'unwatch'
				: 'watch';

			if ( isset( $this->data['action_urls'][$mode] ) ) {
				$this->data['view_urls'][$mode] = $this->data['action_urls'][$mode];
				unset( $this->data['action_urls'][$mode] );
			}
		}

		// Naming conventions for Mustache parameters:
		// - Prefix "is" for boolean values.
		// - Prefix "msg-" for interface messages.
		// - Prefix "page-" for data relating to the current page (e.g. Title, WikiPage, or OutputPage).
		// - Prefix "html-" for raw HTML (in front of other keys, if applicable).
		// - Conditional values are null if absent.
		$params = [
			'html-headelement' => $this->get( 'headelement', '' ),
			'html-sitenotice' => $this->get( 'sitenotice', null ),
			'html-sitebanner' => $this->get( 'sitebanner', null ),
			'html-indicators' => $this->getIndicators(),
			'page-langcode' => $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode(),
			'page-isarticle' => (bool)$this->data['isarticle'],

			// Remember that the string '0' is a valid title.
			// From OutputPage::getPageTitle, via ::setPageTitle().
			'html-title' => $this->get( 'title', '' ),

			'html-prebodyhtml' => $this->get( 'prebodyhtml', '' ),
			'msg-tagline' => $this->getMsg( 'tagline' )->text(),
			// TODO: mediawiki/SkinTemplate should expose langCode and langDir properly.
			'html-userlangattributes' => $this->get( 'userlangattributes', '' ),
			// From OutputPage::getSubtitle()
			'html-subtitle' => $this->get( 'subtitle', '' ),

			// TODO: Use directly Skin::getUndeleteLink() directly.
			// Always returns string, cast to null if empty.
			'html-undelete' => $this->get( 'undelete', null ) ?: null,

			// From Skin::getNewtalks(). Always returns string, cast to null if empty.
			'html-newtalk' => $this->get( 'newtalk', '' ) ?: null,

			'msg-jumptonavigation' => $this->getMsg( 'vectorad-jumptonavigation' )->text(),
			'msg-jumptosearch' => $this->getMsg( 'vectorad-jumptosearch' )->text(),

			// Result of OutputPage::addHTML calls
			'html-bodycontent' => $this->get( 'bodycontent' ),

			'html-printfooter' => $this->get( 'printfooter', null ),
			'html-catlinks' => $this->get( 'catlinks', '' ),
			'html-dataAfterContent' => $this->get( 'dataAfterContent', '' ),
			'html-bottombanner' => $this->get( 'bottombanner', null ),
			// From MWDebug::getHTMLDebugLog (when $wgShowDebug is enabled)
			'html-debuglog' => $this->get( 'debughtml', '' ),
			// From BaseTemplate::getTrail (handles bottom JavaScript)
			'html-printtail' => $this->getTrail(),
		];

		// TODO: Convert the rest to Mustache
		ob_start();
		?>
		<div id="mw-navigation">
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<div id="mw-head">
				<?php $this->renderNavigation( [ 'PERSONAL' ] ); ?>
				<div id="left-navigation">
					<?php $this->renderNavigation( [ 'NAMESPACES', 'VARIANTS' ] ); ?>
				</div>
				<div id="right-navigation">
					<?php $this->renderNavigation( [ 'VIEWS', 'ACTIONS', 'SEARCH' ] ); ?>
				</div>
			</div>
			<div id="mw-panel">
				<div id="p-logo" role="banner"><a class="mw-wiki-logo" href="<?php
					echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] )
					?>" <?php
					echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) )
					?>></a></div>
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
			</div>
		</div>
		<?php Hooks::run( 'VectorAdBeforeFooter' ); ?>
		<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php
			foreach ( $this->getFooterLinks() as $category => $links ) {
			?>
			<ul id="footer-<?php echo $category ?>">
				<?php
				foreach ( $links as $link ) {
				?>
				<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
				<?php
				}
				?>
			</ul>
			<?php
			}
			?>
			<?php $footericons = $this->getFooterIcons( 'icononly' );
			if ( count( $footericons ) > 0 ) {
				?>
				<ul id="footer-icons" class="noprint">
					<?php
					foreach ( $footericons as $blockName => $footerIcons ) {
					?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
						<?php
						foreach ( $footerIcons as $icon ) {
							echo $this->getSkin()->makeFooterIcon( $icon );
						}
						?>
					</li>
					<?php
					}
					?>
				</ul>
			<?php
			}
			?>
			<div style="clear: both;"></div>
		</div>
		<?php
		$params['html-unported'] = ob_get_contents();
		ob_end_clean();

		// Prepare and output the HTML response
		$templates = new TemplateParser( __DIR__ . '/templates' );
		echo $templates->processTemplate( 'index', $params );
	}

	/**
	 * Render a series of portals
	 *
	 * @param array $portals
	 */
	protected function renderPortals( array $portals ) {
		global $wgAdSidebarTopCode, $wgAdSidebarBottomCode;
		global $wgAdSidebarTopType, $wgAdSidebarBottomType;
		/* ------------------------------------------------- //
		   WikiMANNia hack - Add DonationBox and FacebookBox
		// ------------------------------------------------- */
		if ( !empty( $this->getDonationBox() ) ) {
			$portals = array_merge ( $portals, [ 'donations' => $this->getDonationBox() ] );
		}
		if ( !empty( $this->getFacebookBox() ) ) {
			$portals = array_merge ( $portals, [ 'facebook' => $this->getFacebookBox() ] );
		}
		if ( !empty( $this->getAltersklassifizierungBox() ) ) {
			$portals = array_merge ( $portals, [ 'labelled' => $this->getAltersklassifizierungBox() ] );
		}
		/* ------------------------------------------------- */

		// Force the rendering of the following portals
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
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
				case 'AD1':
					if ( !empty( $wgAdSidebarTopCode ) ) {
						$tmp_name = isset($wgAdSidebarTopType) ? $wgAdSidebarTopType : 'advertising';
						$this->renderPortal( 'advertising', $wgAdSidebarTopCode, $tmp_name );
					}
					break;
				case 'AD2':
					if ( !empty( $wgAdSidebarBottomCode ) ) {
						$tmp_name = isset($wgAdSidebarBottomType) ? $wgAdSidebarBottomType : 'advertising';
						$this->renderPortal( 'advertising', $wgAdSidebarBottomCode, $tmp_name );
					}
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					Hooks::run( 'VectorAdAfterToolbox' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
					break;
			}
		}
	}

	/**
	 * @param string $name
	 * @param array|string $content
	 * @param null|string $msg
	 * @param null|string|array $hook
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = $this->getMsg( $msg );
		$labelId = htmlspecialchars( Sanitizer::escapeIdForAttribute( "p-$name-label" ) );
		$body_class = 'body';
		if ( $name === 'advertising' )  $body_class = 'body2';
		if ( $name === 'facebook'  )    $body_class = 'body3';
		if ( $name === 'donations' )    $body_class = 'body3';
		if ( $name === 'labelled' )     $body_class = 'body3';
		?>
		<div class="portal" role="navigation" id="<?php
		echo htmlspecialchars( Sanitizer::escapeIdForAttribute( "p-$name" ) )
		?>" <?php
		echo Linker::tooltip( 'p-' . $name )
		?> aria-labelledby="<?php echo $labelId ?>">
			<h3<?php $this->html( 'userlangattributes' ) ?> id="<?php echo $labelId
				?>"><?php
				echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg );
				?></h3>
			<div class="<?php echo $body_class ?>">
				<?php
				if ( is_array( $content ) ) {
				?>
				<ul>
					<?php
					foreach ( $content as $key => $val ) {
						echo $this->makeListItem( $key, $val );
					}
					if ( $hook !== null ) {
						// Avoid PHP 7.1 warning
						$skin = $this;
						Hooks::run( $hook, [ &$skin, true ] );
					}
					?>
				</ul>
				<?php
				} else {
					// Allow raw HTML block to be defined by extensions
					echo $content;
				}

				$this->renderAfterPortlet( $name );
				?>
			</div>
		</div>
	<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reversed by css
	 * when UI is in RTL mode
	 *
	 * @param array $elements
	 */
	protected function renderNavigation( array $elements ) {
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
					?>
					<div id="p-namespaces" role="navigation" class="vectoradTabs<?php
					if ( count( $this->data['namespace_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-namespaces-label">
						<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['namespace_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'vectorad-wrap' => true,
								] );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'VARIANTS':
					?>
					<div id="p-variants" role="navigation" class="vectoradMenu<?php
					if ( count( $this->data['variant_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-variants-label">
						<?php
						// Replace the label with the name of currently chosen variant, if any
						$variantLabel = $this->getMsg( 'variants' )->text();
						foreach ( $this->data['variant_urls'] as $item ) {
							if ( isset( $item['class'] ) && stripos( $item['class'], 'selected' ) !== false ) {
								$variantLabel = $item['text'];
								break;
							}
						}
						?>
						<input type="checkbox" class="vectoradMenuCheckbox" aria-labelledby="p-variants-label" />
						<h3 id="p-variants-label">
							<span><?php echo htmlspecialchars( $variantLabel ) ?></span>
						</h3>
						<ul class="menu">
							<?php
							foreach ( $this->data['variant_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'VIEWS':
					?>
					<div id="p-views" role="navigation" class="vectoradTabs<?php
					if ( count( $this->data['view_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-views-label">
						<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['view_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'vectorad-wrap' => true,
									'vectorad-collapsible' => true,
								] );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'ACTIONS':
					?>
					<div id="p-cactions" role="navigation" class="vectoradMenu<?php
					if ( count( $this->data['action_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-cactions-label">
						<input type="checkbox" class="vectoradMenuCheckbox" aria-labelledby="p-cactions-label" />
						<h3 id="p-cactions-label"><span><?php
							$this->msg( 'vectorad-more-actions' )
						?></span></h3>
						<ul class="menu" <?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['action_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'PERSONAL':
					?>
					<div id="p-personal" role="navigation"<?php
					if ( count( $this->data['personal_urls'] ) == 0 ) {
						echo ' class="emptyPortlet"';
					}
					?> aria-labelledby="p-personal-label">
						<h3 id="p-personal-label"><?php $this->msg( 'personaltools' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							$notLoggedIn = '';

							if ( !$this->getSkin()->getUser()->isLoggedIn() &&
								User::groupHasPermission( '*', 'edit' )
							) {
								$notLoggedIn =
									Html::element( 'li',
										[ 'id' => 'pt-anonuserpage' ],
										$this->getMsg( 'notloggedin' )->text()
									);
							}

							$personalTools = $this->getPersonalTools();

							$langSelector = '';
							if ( array_key_exists( 'uls', $personalTools ) ) {
								$langSelector = $this->makeListItem( 'uls', $personalTools[ 'uls' ] );
								unset( $personalTools[ 'uls' ] );
							}

							echo $langSelector;
							echo $notLoggedIn;
							foreach ( $personalTools as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'SEARCH':
					?>
					<div id="p-search" role="search">
						<h3<?php $this->html( 'userlangattributes' ) ?>>
							<label for="searchInput"><?php $this->msg( 'search' ) ?></label>
						</h3>
						<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
							<div<?php echo $this->config->get( 'VectorAdUseSimpleSearch' ) ? ' id="simpleSearch"' : '' ?>>
								<?php
								echo $this->makeSearchInput( [ 'id' => 'searchInput' ] );
								echo Html::hidden( 'title', $this->get( 'searchtitle' ) );
								/* We construct two buttons (for 'go' and 'fulltext' search modes),
								 * but only one will be visible and actionable at a time (they are
								 * overlaid on top of each other in CSS).
								 * * Browsers will use the 'fulltext' one by default (as it's the
								 *   first in tree-order), which is desirable when they are unable
								 *   to show search suggestions (either due to being broken or
								 *   having JavaScript turned off).
								 * * The mediawiki.searchSuggest module, after doing tests for the
								 *   broken browsers, removes the 'fulltext' button and handles
								 *   'fulltext' search itself; this will reveal the 'go' button and
								 *   cause it to be used.
								 */
								echo $this->makeSearchButton(
									'fulltext',
									[ 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ]
								);
								echo $this->makeSearchButton(
									'go',
									[ 'id' => 'searchButton', 'class' => 'searchButton' ]
								);
								?>
							</div>
						</form>
					</div>
					<?php

					break;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function makeLink( $key, $item, $options = [] ) {
		$html = parent::makeLink( $key, $item, $options );
		// Add an extra wrapper because our CSS is weird
		if ( isset( $options['vectorad-wrap'] ) && $options['vectorad-wrap'] ) {
			$html = Html::rawElement( 'span', [], $html );
		}
		return $html;
	}

	/**
	 * @inheritDoc
	 */
	public function makeListItem( $key, $item, $options = [] ) {
		// For fancy styling of watch/unwatch star
		if (
			$this->config->get( 'VectorAdUseIconWatch' )
			&& ( $key === 'watch' || $key === 'unwatch' )
		) {
			$item['class'] = rtrim( 'icon ' . $item['class'], ' ' );
			$item['primary'] = true;
		}

		// Add CSS class 'collapsible' to links which are not marked as "primary"
		if (
			isset( $options['vectorad-collapsible'] ) && $options['vectorad-collapsible'] ) {
			$item['class'] = rtrim( 'collapsible ' . $item['class'], ' ' );
		}

		// We don't use this, prevent it from popping up in HTML output
		unset( $item['redundant'] );

		return parent::makeListItem( $key, $item, $options );
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

		return $html;
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

		return $this->getWimaBox( $wgFacebookButton, $wgFacebookButtonIMG, $wgFacebookButtonURL, 'Facebook-Button', 'width:148px; height:57px;' );
	}

	/**
	 * Renderer for Altersklassifizierung block
	 *
	 * @return string html
	 */
	private function getAltersklassifizierungBox() {
		global $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL;

		return $this->getWimaBox( $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL, 'AgeClassification-Button', 'width:148px; height:28px;' );
	}
}
