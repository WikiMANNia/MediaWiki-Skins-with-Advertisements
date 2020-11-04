<?php
/**
 * Wima nouveau.
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
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
 * @ingroup Skins
 */
class WimaTemplate extends BaseTemplate {

	/**
	 * Template filter callback for Wima skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	public function execute() {
		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

		$this->html( 'headelement' );
		?><div id="globalWrapper">
		<div id="column-content">
			<div id="content" class="mw-body" role="main">
				<a id="top"></a>
				<?php
				echo $this->getSitenoticeOrAdvertisementBox();
				echo $this->getIndicators();
				// Loose comparison with '!=' is intentional, to catch null and false too, but not '0'
				if ( $this->data['title'] != '' ) {
				?>
				<h1 id="firstHeading" class="firstHeading" lang="<?php
				$this->data['pageLanguage'] =
					$this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();
				$this->text( 'pageLanguage' );
				?>"><?php $this->html( 'title' ) ?></h1>
				<?php } ?>

				<div id="bodyContent" class="mw-body-content">
					<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
					<div id="contentSub"<?php
					$this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' )
						?></div>
					<?php if ( $this->data['undelete'] ) { ?>
						<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
					<?php
}
					?><?php
					if ( $this->data['newtalk'] ) {
						?>
						<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
					<?php
					}
					?>
					<div id="jump-to-nav" class="mw-jump"><?php
						$this->msg( 'jumpto' )
						?> <a href="#column-one"><?php
							$this->msg( 'jumptonavigation' )
							?></a><?php
						$this->msg( 'comma-separator' )
						?><a href="#searchInput"><?php
							$this->msg( 'jumptosearch' )
							?></a></div>

					<!-- start content -->
					<?php $this->html( 'bodytext' ) ?>
					<?php
					if ( $this->data['catlinks'] ) {
						$this->html( 'catlinks' );
					}
					?>
					<!-- end content -->
					<?php
					if ( $this->data['dataAfterContent'] ) {
						$this->html( 'dataAfterContent'
						);
					}
					echo $this->getAdvertisementBoxUnten();
					?>
					<div class="visualClear"></div>
				</div>
			</div>
		</div>
		<div id="column-one"<?php $this->html( 'userlangattributes' ) ?>>
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<?php $this->cactions(); ?>
			<div class="portlet" id="p-personal" role="navigation">
				<h3><?php $this->msg( 'personaltools' ) ?></h3>

				<div class="pBody">
					<ul<?php $this->html( 'userlangattributes' ) ?>>
						<?php

						$personalTools = $this->getPersonalTools();

						if ( array_key_exists( 'uls', $personalTools ) ) {
							echo $this->makeListItem( 'uls', $personalTools[ 'uls' ] );
							unset( $personalTools[ 'uls' ] );
						}

						if ( !$this->getSkin()->getUser()->isLoggedIn() &&
							User::groupHasPermission( '*', 'edit' ) ) {

							echo Html::rawElement( 'li', array(
								'id' => 'pt-anonuserpage'
							), $this->getMsg( 'notloggedin' )->escaped() );

						}

						foreach ( $personalTools as $key => $item ) { ?>
							<?php echo $this->makeListItem( $key, $item ); ?>

						<?php
}
						?>
					</ul>
				</div>
			</div>
			<div class="portlet" id="p-logo" role="banner">
				<?php
				echo Html::element( 'a', array(
						'href' => $this->data['nav_urls']['mainpage']['href'],
						'class' => 'mw-wiki-logo',
						)
						+ Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
				); ?>

			</div>
			<?php
			$this->renderPortals( $this->data['sidebar'] );
			?>
		</div><!-- end of the left (by default at least) column -->
		<div class="visualClear"></div>
		<?php
		$validFooterIcons = $this->getFooterIcons( "icononly" );
		$validFooterLinks = $this->getFooterLinks( "flat" ); // Additional footer links

		if ( count( $validFooterIcons ) + count( $validFooterLinks ) > 0 ) {
			?>
			<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php
			$footerEnd = '</div>';
		} else {
			$footerEnd = '';
		}

		foreach ( $validFooterIcons as $blockName => $footerIcons ) {
			?>
			<div id="f-<?php echo htmlspecialchars( $blockName ); ?>ico">
				<?php foreach ( $footerIcons as $icon ) { ?>
					<?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

				<?php
}
				?>
			</div>
		<?php
		}

		if ( count( $validFooterLinks ) > 0 ) {
			?>
			<ul id="f-list">
				<?php
				foreach ( $validFooterLinks as $aLink ) {
					?>
					<li id="<?php echo $aLink ?>"><?php $this->html( $aLink ) ?></li>
				<?php
				}
				?>
			</ul>
		<?php
		}

		echo $footerEnd;
		?>

		</div>
		<?php
		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
		echo "\n";
		wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/

	/**
	 * @param array $sidebar
	 */
	protected function renderPortals( $sidebar ) {
		global $wgAdSidebarTopCode, $wgAdSidebarBottomCode;
		global $wgAdSidebarTopType, $wgAdSidebarBottomType;

		if ( !isset( $sidebar['SEARCH'] ) ) {
			$sidebar['SEARCH'] = true;
		}
		if ( !isset( $sidebar['TOOLBOX'] ) ) {
			$sidebar['TOOLBOX'] = true;
		}
		if ( !isset( $sidebar['LANGUAGES'] ) ) {
			$sidebar['LANGUAGES'] = true;
		}

		foreach ( $sidebar as $boxName => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$boxName = (string)$boxName;

			if ( $boxName == 'SEARCH' ) {
				$this->searchBox();
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'AD1' ) {
				if ( isset($wgAdSidebarTopType) ) {
					$tmp_name = isset($wgAdSidebarTopType) ? $wgAdSidebarTopType : 'advertising';
					$this->customBox( 'advertising', $wgAdSidebarTopCode, $tmp_name );
				}
			} elseif ( $boxName == 'AD2' ) {
				if ( isset($wgAdSidebarBottomCode) ) {
					$tmp_name = isset($wgAdSidebarBottomType) ? $wgAdSidebarBottomType : 'advertising';
					$this->customBox( 'advertising', $wgAdSidebarBottomCode, $tmp_name );
				}
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $content );
			}
		}
		// Add box for donation page and Facebook linking
		// Füge Box für Spendenseite und Facebook-Verlinkung hinzu
		echo $this->getWimaBoxes();
	}

	function searchBox() {
		?>
		<div id="p-search" class="portlet" role="search">
			<h3><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h3>

			<div id="searchBody" class="pBody">
				<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
					<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
					<?php echo $this->makeSearchInput( array( "id" => "searchInput" ) ); ?>

					<?php
					echo $this->makeSearchButton(
						"go",
						array( "id" => "searchGoButton", "class" => "searchButton" )
					);

					if ( $this->config->get( 'UseTwoButtonsSearchForm' ) ) {
						?>&#160;
						<?php echo $this->makeSearchButton(
							"fulltext",
							array( "id" => "mw-searchButton", "class" => "searchButton" )
						);
					} else {
						?>

						<div><a href="<?php
						$this->text( 'searchaction' )
						?>" rel="search"><?php $this->msg( 'powersearch-legend' ) ?></a></div><?php
					} ?>

				</form>

				<?php $this->renderAfterPortlet( 'search' ); ?>
			</div>
		</div>
	<?php
	}

	/**
	 * Prints the cactions bar.
	 * Shared between Wima, MonoBook and Modern
	 */
	function cactions() {
		?>
		<div id="p-cactions" class="portlet" role="navigation">
			<h3><?php $this->msg( 'views' ) ?></h3>

			<div class="pBody">
				<ul><?php
					foreach ( $this->data['content_actions'] as $key => $tab ) {
						echo '
				' . $this->makeListItem( $key, $tab );
					} ?>

				</ul>
				<?php $this->renderAfterPortlet( 'cactions' ); ?>
			</div>
		</div>
	<?php
	}

	/*************************************************************************************************/
	function toolbox() {
		?>
		<div class="portlet" id="p-tb" role="navigation">
			<h3><?php $this->msg( 'toolbox' ) ?></h3>

			<div class="pBody">
				<ul>
					<?php
					foreach ( $this->getToolbox() as $key => $tbitem ) {
						?>
						<?php echo $this->makeListItem( $key, $tbitem ); ?>

					<?php
					}

					// Avoid PHP 7.1 warnings
					$skin = $this;
					Hooks::run( 'WimaTemplateToolboxEnd', [ &$skin ] );
					Hooks::run( 'SkinTemplateToolboxEnd', [ &$skin, true ] );
					?>
				</ul>
				<?php $this->renderAfterPortlet( 'tb' ); ?>
			</div>
		</div>
	<?php
	}

	/*************************************************************************************************/
	function languageBox() {
		if ( $this->data['language_urls'] !== false ) {
			?>
			<div id="p-lang" class="portlet" role="navigation">
				<h3<?php $this->html( 'userlangattributes' ) ?>><?php $this->msg( 'otherlanguages' ) ?></h3>

				<div class="pBody">
					<ul>
						<?php foreach ( $this->data['language_urls'] as $key => $langlink ) { ?>
							<?php echo $this->makeListItem( $key, $langlink ); ?>

						<?php
}
						?>
					</ul>

					<?php $this->renderAfterPortlet( 'lang' ); ?>
				</div>
			</div>
		<?php
		}
	}

	/*************************************************************************************************/
	/**
	 * @param string $bar
	 * @param array|string $cont
	 */
	function customBox( $bar, $cont, $msg = null ) {
		if ( $msg === null ) {
			$msg = $bar;
		} else {
			$msg = 'wima-' . $msg;
		}
		$body_class = ( $bar === 'advertising' ) ? 'pBodyAd' : 'pBody';
		$msgObj = wfMessage( $msg );
		$msg = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg );
		$portletAttribs = array(
			'class' => 'generated-sidebar portlet',
			'id' => Sanitizer::escapeId( "p-$bar" ),
			'role' => 'navigation'
		);

		$tooltip = Linker::titleAttrib( "p-$bar" );
		if ( $tooltip !== false ) {
			$portletAttribs['title'] = $tooltip;
		}
		echo '	' . Html::openElement( 'div', $portletAttribs );
		?>

		<h3><?php echo $msg; ?></h3>
		<div class="<?php echo $body_class ?>">
			<?php
			if ( is_array( $cont ) ) {
				?>
				<ul>
					<?php
					foreach ( $cont as $key => $val ) {
						?>
						<?php echo $this->makeListItem( $key, $val ); ?>

					<?php
					}
					?>
				</ul>
			<?php
			} else {
				# allow raw HTML block to be defined by extensions
				print $cont;
			}

			$this->renderAfterPortlet( $bar );
			?>
		</div>
		</div>
	<?php
	}

	/**
	 * Renderer for advertisement block
	 *
	 * @return string html
	 */
	private function getSitenoticeOrAdvertisementBox() {
		global $wgTopBannerCode;
		$html = '';
		$issetSitenoticeBox    = $this->data['sitenotice'];
		$issetAdvertisementBox = isset($wgTopBannerCode);

		if ($issetSitenoticeBox && $issetAdvertisementBox) {
			if ( rand(0, 1) ) {
				$html = $this->getSitenoticeBox();
			} else {
				$html = $this->getAdvertisementBoxOben();
			}
		} elseif ($issetSitenoticeBox) {
			$html = $this->getSitenoticeBox();
		} elseif ($issetAdvertisementBox) {
			$html = $this->getAdvertisementBoxOben();
		}

		return $html;
	}
	private function getSitenoticeBox() {
		return '<div id="siteNotice">' . $this->html( "sitenotice" ) . '</div>';
	}
	private function getAdvertisementBoxOben() {
		global $wgTopBannerCode, $wgTopBannerStyle, $wgTopBannerType;
		$style1 = 'text-align:left;';
		$style2 = isset($wgTopBannerStyle) ? $wgTopBannerStyle : 'border:1px solid blue; text-align:center;';

		return $this->getAdvertisementBox($wgTopBannerCode, $wgTopBannerType, $style1, $style2);
	}
	private function getAdvertisementBoxUnten() {
		global $wgBottomBannerCode, $wgBottomBannerStyle, $wgBottomBannerType;
		$style1 = 'clear:both; margin-top:1em; text-align:left;';
		$style2 = isset($wgBottomBannerStyle) ? $wgBottomBannerStyle : 'border:0; text-align:center;';

		return $this->getAdvertisementBox($wgBottomBannerCode, $wgBottomBannerType, $style1, $style2);
	}
	private function getAdvertisementBox($code, $type, $style1, $style2) {

		if ( isset($code) ) {
			$msg_key = isset($type) ? $type : 'advertising';
			return '<div id="p-advertising" title="Link mit Skripte" style="' . $style1 . '">'
			      . $this->getMsg( 'wima-'.$msg_key )->text() . ':'
			      . '<div style="' . $style2 . '">'
			      . $code
			      . '</div></div>';
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
			$html = '<div class="portlet" id="p-donations" role="navigation">';
			$html .= '<h3 id="p-donations-label">' . $this->getMsg( 'wima-donations' )->text() . '</h3>';
			$html .= '<div title="Reiner Link ohne Skripte" class="body" align="center">';
			$html .= '<a href="//' . $wgDonationButtonURL . '"><img alt="Donate-Button" src="//' . $tmpServerDomain . $tmpDonationButtonIMG . '" style="margin-top:6px; width:92px; height:26px;" /></a>';
			$html .= '</div></div>';
		}

		return $html;
	}

	/**
	 * Renderer for wima block
	 *
	 * @return string html
	 */
	private function getWimaBox( $buttonActive, $buttonIMG, $buttonURL, $buttonLabel, $buttonAlt, $buttonStyle ) {

		if ( empty($buttonActive) ) return ''; // Do nothing
		if ( empty($buttonIMG) ) return ''; // Do nothing
		if ( empty($buttonURL) ) return ''; // Do nothing
		$html = '';

		if (($buttonActive === 'true') || ($buttonActive === true)) {
			$html = '<div class="portlet" id="p-'.$buttonLabel.'" role="navigation">';
			$html .= '<h3 id="p-'.$buttonLabel.'-label">' . $this->getMsg( 'wima-'.$buttonLabel )->text() . '</h3>';
			$html .= '<div title="Reiner Link ohne Skripte" class="body" align="center" style="margin-left:-5px;">';
			$html .= '<a href="//' . $buttonURL . '"><img alt="'.$buttonAlt.'" src="//'.$buttonIMG.'" style="'.$buttonStyle.'" /></a>';
			$html .= '</div></div>';
		}

		return $html;
	}

	/**
	 * Renderer for wima block
	 *
	 * @return string html
	 */
	private function getWimaBoxes() {
		global $wgFacebookButton, $wgFacebookButtonIMG, $wgFacebookButtonURL;
		global $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL;
		$html = '';
		$html .= $this->getDonationBox();
		$html .= $this->getWimaBox( $wgFacebookButton, $wgFacebookButtonIMG, $wgFacebookButtonURL, 'facebook', 'Facebook-Button', 'width:148px; height:57px;' );
		$html .= $this->getWimaBox( $wgAgeClassificationButton, $wgAgeClassificationButtonIMG, $wgAgeClassificationButtonURL, 'labelled', 'AgeClassification-Button', 'width:148px; height:28px;' );

		return $html;
	}
} // end of class
