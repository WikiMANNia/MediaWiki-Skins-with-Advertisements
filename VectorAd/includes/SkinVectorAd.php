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
 * SkinTemplate class for VectorAd skin
 * @ingroup Skins
 */
class SkinVectorAd extends SkinTemplate {
	public $skinname = 'vectorad';
	public $stylename = 'VectorAd';
	public $template = 'VectorAdTemplate';
	/**
	 * @var Config
	 */
	private $vectoradConfig;

	public function __construct() {
		$this->vectoradConfig = ConfigFactory::getDefaultInstance()->makeConfig( 'vectorad' );
	}

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param OutputPage $out Object to initialize
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		if ( $this->vectoradConfig->get( 'VectorAdResponsive' ) ) {
			$out->addMeta( 'viewport', 'width=device-width, initial-scale=1' );
			$out->addModuleStyles( 'skins.vector.styles.responsive' );
		}

		$out->addModules( 'skins.vector.js' );
	}

	/**
	 * Loads skin and user CSS files.
	 * @param OutputPage $out
	 */
	public function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$styles = [ 'mediawiki.skinning.interface', 'skins.vector.styles' ];
		Hooks::run( 'SkinVectorStyleModules', [ $this, &$styles ] );
		$out->addModuleStyles( $styles );
	}

	/**
	 * Override to pass our Config instance to it
	 */
	public function setupTemplate( $classname, $repository = false, $cache_dir = false ) {
		return new $classname( $this->vectoradConfig );
	}
}
