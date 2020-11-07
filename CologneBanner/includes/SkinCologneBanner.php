<?php
/**
 * Cologne Banner: A nicer-looking alternative to Standard.
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
 * @todo document
 * @file
 * @ingroup Skins
 */

/**
 * @todo document
 * @ingroup Skins
 */
class SkinCologneBanner extends SkinTemplate {
	public $skinname = 'colognebanner';
	public $template = 'CologneBannerTemplate';

	/**
	 * @param OutputPage $out
	 */
	public function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'mediawiki.legacy.oldshared' );
		$out->addModuleStyles( 'skins.cologneblue' );

		// FSM-Altersklassifizierungssystems: www.altersklassifizierung.de
		global $wgAgeClassificationMetaName, $wgAgeClassificationMetaContent;
		if ( !empty( $wgAgeClassificationMetaName ) AND !empty( $wgAgeClassificationMetaContent ) ) {
			$out->addMeta( $wgAgeClassificationMetaName, $wgAgeClassificationMetaContent );
		}
	}

	/**
	 * Override langlink formatting behavior not to uppercase the language names.
	 * See otherLanguages() in CologneBannerTemplate.
	 * @param string $name
	 * @return string
	 */
	public function formatLanguageName( $name ) {
		return $name;
	}
}
