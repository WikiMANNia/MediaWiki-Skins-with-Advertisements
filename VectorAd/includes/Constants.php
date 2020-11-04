<?php
namespace VectorAd;

use FatalError;

/**
 * A namespace for VectorAd constants for internal VectorAd usage only. **Do not rely on this file as an
 * API as it may change without warning at any time.**
 */
final class Constants {
	/**
	 * This is tightly coupled to the ConfigRegistry field in skin.json.
	 * @var string
	 */
	public const SKIN_NAME = 'vectorad';

	// These are tightly coupled to PREF_KEY_SKIN_VERSION and skin.json's configs. See skin.json for
	// documentation.
	/**
	 * @var string
	 */
	public const SKIN_VERSION_LEGACY = '1';
	/**
	 * @var string
	 */
	public const SKIN_VERSION_LATEST = '2';

	/**
	 * @var string
	 */
	public const SERVICE_CONFIG = 'VectorAd.Config';

	/**
	 * @var string
	 */
	public const SERVICE_FEATURE_MANAGER = 'VectorAd.FeatureManager';

	// These are tightly coupled to skin.json's config.
	/**
	 * @var string
	 */
	public const CONFIG_KEY_SHOW_SKIN_PREFERENCES = 'VectorAdShowSkinPreferences';
	/**
	 * @var string
	 */
	public const CONFIG_KEY_DEFAULT_SKIN_VERSION = 'VectorAdDefaultSkinVersion';
	/**
	 * @var string
	 */
	public const CONFIG_KEY_DEFAULT_SKIN_VERSION_FOR_EXISTING_ACCOUNTS =
		'VectorAdDefaultSkinVersionForExistingAccounts';
	/**
	 * @var string
	 */
	public const CONFIG_KEY_DEFAULT_SKIN_VERSION_FOR_NEW_ACCOUNTS =
		'VectorAdDefaultSkinVersionForNewAccounts';

	/**
	 * @var string
	 */
	public const CONFIG_KEY_DEFAULT_SIDEBAR_VISIBLE_FOR_AUTHORISED_USER =
		'VectorAdDefaultSidebarVisibleForAuthorisedUser';

	/**
	 * @var string
	 */
	public const CONFIG_KEY_DEFAULT_SIDEBAR_VISIBLE_FOR_ANONYMOUS_USER =
		'VectorAdDefaultSidebarVisibleForAnonymousUser';

	/**
	 * @var string
	 */
	public const CONFIG_KEY_LAYOUT_MAX_WIDTH = 'VectorAdLayoutMaxWidth';

	/**
	 * @var string
	 */
	public const PREF_KEY_SKIN_VERSION = 'VectorAdSkinVersion';

	/**
	 * @var string
	 */
	public const PREF_KEY_SIDEBAR_VISIBLE = 'VectorAdSidebarVisible';

	// These are used in the Feature Management System.
	/**
	 * Also known as `$wgFullyInitialised`. Set to true in core/includes/Setup.php.
	 * @var string
	 */
	public const CONFIG_KEY_FULLY_INITIALISED = 'FullyInitialised';

	/**
	 * @var string
	 */
	public const REQUIREMENT_FULLY_INITIALISED = 'FullyInitialised';

	/**
	 * @var string
	 */
	public const REQUIREMENT_LATEST_SKIN_VERSION = 'LatestSkinVersion';

	/**
	 * @var string
	 */
	public const FEATURE_LATEST_SKIN = 'LatestSkin';

	// These are used for query parameters.
	/**
	 * Override the skin version user preference and site Config. See readme.
	 * @var string
	 */
	public const QUERY_PARAM_SKIN_VERSION = 'useskinversion';

	/**
	 * This class is for namespacing constants only. Forbid construction.
	 * @throws FatalError
	 */
	private function __construct() {
		throw new FatalError( "Cannot construct a utility class." );
	}
}
