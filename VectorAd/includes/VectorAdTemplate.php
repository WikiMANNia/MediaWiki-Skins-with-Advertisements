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
 * QuickTemplate class for VectorAd skin
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

		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];

		// Move the watch/unwatch star outside of the collapsed "actions" menu to the main "views" menu
		if ( $this->config->get( 'VectorAdUseIconWatch' ) ) {
			$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() )
				? 'unwatch'
				: 'watch';

			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

		$this->data['pageLanguage'] =
			$this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();

		// Output HTML Page
		$this->html( 'headelement' );
		?>
		<div id="mw-page-base" class="noprint"></div>
		<div id="mw-head-base" class="noprint"></div>
		<div id="content" class="mw-body" role="main">
			<a id="top"></a>
			<?php
			// Randomly display either the sitenotice or the banner ad.
			// Zeige zufallsgesteuert entweder die Sitenotice oder den Werbebanner an.
			if ($issetSitenoticeBox && $issetAdvertisementBox) {
				if ( rand(0, 1) ) {
					$this->data['sitenotice'] = $this->getSitenoticeBox();
					$this->data['sitebanner'] = null;
				} else {
					$this->data['sitenotice'] = null;
					$this->data['sitebanner'] = $this->getAdvertisementBoxOben();
				}
			} elseif ($issetSitenoticeBox) {
				$this->data['sitenotice'] = $this->getSitenoticeBox();
				$this->data['sitebanner'] = null;
			} elseif ($issetAdvertisementBox) {
				$this->data['sitenotice'] = null;
				$this->data['sitebanner'] = $this->getAdvertisementBoxOben();
			}

			if ( is_callable( [ $this, 'getIndicators' ] ) ) {
				echo $this->getIndicators();
			}
			// Loose comparison with '!=' is intentional, to catch null and false too, but not '0'
			if ( $this->data['title'] != '' ) {
			?>
			<h1 id="firstHeading" class="firstHeading" lang="<?php $this->text( 'pageLanguage' ); ?>"><?php
				 $this->html( 'title' )
			?></h1>
			<?php
			} ?>
			<?php $this->html( 'prebodyhtml' ) ?>
			<div id="bodyContent" class="mw-body-content">
				<?php
				if ( $this->data['isarticle'] ) {
					?>
					<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
				<?php
				}
				?>
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php
					$this->html( 'subtitle' )
				?></div>
				<?php
				if ( $this->data['undelete'] ) {
					?>
					<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<?php
				}
				if ( $this->data['newtalk'] ) {
					?>
					<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
				<?php
				}
				?>
				<div id="jump-to-nav" class="mw-jump">
					<?php $this->msg( 'jumpto' ) ?>
					<a href="#mw-head"><?php
						$this->msg( 'jumptonavigation' )
					?></a><?php $this->msg( 'comma-separator' ) ?>
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<?php
				$this->html( 'bodycontent' );

				if ( $this->data['printfooter'] ) {
					?>
					<div class="printfooter">
						<?php $this->html( 'printfooter' ); ?>
					</div>
				<?php
				}

				if ( $this->data['catlinks'] ) {
					$this->html( 'catlinks' );
				}

				if ( $this->data['dataAfterContent'] ) {
					$this->html( 'dataAfterContent' );
				}

				$this->data['bottombanner'] = $this->getAdvertisementBoxUnten();
				?>
				<div class="visualClear"></div>
				<?php $this->html( 'debughtml' ); ?>
			</div>
		</div>
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
		<?php $this->printTrail(); ?>

	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param array $portals
	 */
	protected function renderPortals( $portals ) {
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
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
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
	 * @param array $content
	 * @param null|string $msg
	 * @param null|string|array $hook
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = wfMessage( $msg );
		$labelId = htmlspecialchars( Sanitizer::escapeId( "p-$name-label" ) );
		$body_class = 'body';
		if ( $name === 'advertising' )  $body_class = 'body2';
		if ( $name === 'facebook'  )    $body_class = 'body3';
		if ( $name === 'donations' )    $body_class = 'body3';
		if ( $name === 'labelled' )     $body_class = 'body3';
		?>
		<div class="portal" role="navigation" id="<?php
		echo htmlspecialchars( Sanitizer::escapeId( "p-$name" ) )
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
	 * Render one or more navigations elements by name, automatically reversed
	 * when UI is in RTL mode
	 *
	 * @param array $elements
	 */
	protected function renderNavigation( $elements ) {
		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = [ $elements ];
			// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $this->data['rtl'] ) {
			$elements = array_reverse( $elements );
		}
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
							foreach ( $this->data['namespace_urls'] as $link ) {
								?>
								<li <?php echo $link['attributes'] ?>><span><a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'];
										if ( isset ( $link['rel'] ) ) {
											echo ' rel="' . htmlspecialchars( $link['rel'] ) . '"';
										}
										?>><?php
											echo htmlspecialchars( $link['text'] )
											?></a></span></li>
							<?php
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
						foreach ( $this->data['variant_urls'] as $link ) {
							if ( stripos( $link['attributes'], 'selected' ) !== false ) {
								$variantLabel = $link['text'];
								break;
							}
						}
						?>
						<h3 id="p-variants-label">
							<span><?php echo htmlspecialchars( $variantLabel ) ?></span><a href="#"></a>
						</h3>
						<ul class="menu">
							<?php
							foreach ( $this->data['variant_urls'] as $link ) {
								?>
								<li<?php echo $link['attributes'] ?>><a href="<?php
									echo htmlspecialchars( $link['href'] )
									?>" lang="<?php
									echo htmlspecialchars( $link['lang'] )
									?>" hreflang="<?php
									echo htmlspecialchars( $link['hreflang'] )
									?>" <?php
									echo $link['key']
									?>><?php
										echo htmlspecialchars( $link['text'] )
										?></a></li>
							<?php
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
							foreach ( $this->data['view_urls'] as $link ) {
								?>
								<li<?php echo $link['attributes'] ?>><span><a href="<?php
										echo htmlspecialchars( $link['href'] )
										?>" <?php
										echo $link['key'];
										if ( isset ( $link['rel'] ) ) {
											echo ' rel="' . htmlspecialchars( $link['rel'] ) . '"';
										}
										?>><?php
											// $link['text'] can be undefined - bug 27764
											if ( array_key_exists( 'text', $link ) ) {
												echo array_key_exists( 'img', $link )
													? '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />'
													: htmlspecialchars( $link['text'] );
											}
											?></a></span></li>
							<?php
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
						<h3 id="p-cactions-label"><span><?php
							$this->msg( 'vectorad-more-actions' )
						?></span><a href="#"></a></h3>

						<ul class="menu" <?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['action_urls'] as $link ) {
								?>
								<li<?php echo $link['attributes'] ?>>
									<a href="<?php
									echo htmlspecialchars( $link['href'] )
									?>" <?php
									echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] )
										?></a>
								</li>
							<?php
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

							if ( !$this->data[ 'rtl' ] ) {
								echo $langSelector;
								echo $notLoggedIn;
							}

							foreach ( $personalTools as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}

							if ( $this->data[ 'rtl' ] ) {
								echo $notLoggedIn;
								echo $langSelector;
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
							// We construct two buttons (for 'go' and 'fulltext' search modes),
							// but only one will be visible and actionable at a time (they are
							// overlaid on top of each other in CSS).
							// * Browsers will use the 'fulltext' one by default (as it's the
							//   first in tree-order), which is desirable when they are unable
							//   to show search suggestions (either due to being broken or
							//   having JavaScript turned off).
							// * The mediawiki.searchSuggest module, after doing tests for the
							//   broken browsers, removes the 'fulltext' button and handles
							//   'fulltext' search itself; this will reveal the 'go' button and
							//   cause it to be used.
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
	 * Renderer for advertisement block
	 *
	 * @return string html
	 */
	private function getSitenoticeBox() {
		echo Html::rawElement( 'div',
			[
				'id' => 'siteNotice',
				'class' => 'mw-body-content',
			],
			// Raw HTML
			$this->get( 'sitenotice' )
		);
		return;
	}
	private function getAdvertisementBoxOben() {
		global $wgTopBannerCode, $wgTopBannerStyle, $wgTopBannerType;
		$id     = 'siteNotice';
		$class  = 'mw-body-adbox';
		$style1 = 'text-align:left;';
		$style2 = isset($wgTopBannerStyle) ? $wgTopBannerStyle : 'border:1px solid blue; font-size:0.8em; text-align:center;';

		return $this->getAdvertisementBox( $wgTopBannerCode, $wgTopBannerType, $style1, $style2, $id, $class );
	}
	private function getAdvertisementBoxUnten() {
		global $wgBottomBannerCode, $wgBottomBannerStyle, $wgBottomBannerType;
		$id     = 'adbox-bottom';
		$class  = 'mw-body-adbox';
		$style1 = 'clear:both; margin-top:1em; text-align:left;';
		$style2 = isset($wgBottomBannerStyle) ? $wgBottomBannerStyle : 'border:0; font-size:0.8em; text-align:center;';

		return $this->getAdvertisementBox( $wgBottomBannerCode, $wgBottomBannerType, $style1, $style2, $id, $class );
	}
	private function getAdvertisementBox( $code, $type, $style1, $style2, $id, $class ) {

		if ( isset( $code ) ) {
			$msg_key = isset($type) ? $type : 'advertising';
			echo '<div id="' . $id . '" class="' . $class . '" title="Link mit Skripte" style="' . $style1 . '">'
			      . $this->getMsg( 'vectorad-' . $msg_key ) . ':'
			      . '<div style="' . $style2 . '">'
			      . $code
			      . '</div></div>';
		}

		return;
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
