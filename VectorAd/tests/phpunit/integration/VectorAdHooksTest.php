<?php
/*
 * @file
 * @ingroup skins
 */

use VectorAd\Hooks;

const SKIN_PREFS_SECTION = 'rendering/skin/skin-prefs';

/**
 * Integration tests for VectorAd Hooks.
 *
 * @group VectorAd
 * @coversDefaultClass \VectorAd\Hooks
 */
class VectorAdHooksTest extends \MediaWikiTestCase {
	/**
	 * @covers ::onGetPreferences
	 */
	public function testOnGetPreferencesShowPreferencesDisabled() {
		$config = new HashConfig( [
			'VectorAdShowSkinPreferences' => false,
		] );
		$this->setService( 'VectorAd.Config', $config );

		$prefs = [];
		Hooks::onGetPreferences( $this->getTestUser()->getUser(), $prefs );
		$this->assertSame( $prefs, [], 'No preferences are added.' );
	}

	/**
	 * @covers ::onGetPreferences
	 */
	public function testOnGetPreferencesShowPreferencesEnabledSkinSectionFoundLegacy() {
		$config = new HashConfig( [
			'VectorAdShowSkinPreferences' => true,
			// '1' is Legacy.
			'VectorAdDefaultSkinVersionForExistingAccounts' => '1',
			'VectorAdDefaultSidebarVisibleForAuthorisedUser' => true
		] );
		$this->setService( 'VectorAd.Config', $config );

		$prefs = [
			'foo' => [],
			'skin' => [],
			'bar' => []
		];
		Hooks::onGetPreferences( $this->getTestUser()->getUser(), $prefs );
		$this->assertSame(
			$prefs,
			[
				'foo' => [],
				'skin' => [],
				'VectorAdSkinVersion' => [
					'type' => 'toggle',
					'label-message' => 'prefs-vectorad-enable-vectorad-1-label',
					'help-message' => 'prefs-vectorad-enable-vectorad-1-help',
					'section' => SKIN_PREFS_SECTION,
					// '1' is enabled which means Legacy.
					'default' => '1',
					'hide-if' => [ '!==', 'wpskin', 'vectorad' ]
				],
				'VectorAdSidebarVisible' => [
					'type' => 'api',
					'default' => true
				],
				'bar' => []
			],
			'Preferences are inserted directly after skin.'
		);
	}

	/**
	 * @covers ::onGetPreferences
	 */
	public function testOnGetPreferencesShowPreferencesEnabledSkinSectionMissingLegacy() {
		$config = new HashConfig( [
			'VectorAdShowSkinPreferences' => true,
			// '1' is Legacy.
			'VectorAdDefaultSkinVersionForExistingAccounts' => '1',
			'VectorAdDefaultSidebarVisibleForAuthorisedUser' => true
		] );
		$this->setService( 'VectorAd.Config', $config );

		$prefs = [
			'foo' => [],
			'bar' => []
		];
		Hooks::onGetPreferences( $this->getTestUser()->getUser(), $prefs );
		$this->assertSame(
			$prefs,
			[
				'foo' => [],
				'bar' => [],
				'VectorAdSkinVersion' => [
					'type' => 'toggle',
					'label-message' => 'prefs-vectorad-enable-vectorad-1-label',
					'help-message' => 'prefs-vectorad-enable-vectorad-1-help',
					'section' => SKIN_PREFS_SECTION,
					// '1' is enabled which means Legacy.
					'default' => '1',
					'hide-if' => [ '!==', 'wpskin', 'vectorad' ]
				],
				'VectorAdSidebarVisible' => [
					'type' => 'api',
					'default' => true
				],
			],
			'Preferences are appended.'
		);
	}

	/**
	 * @covers ::onGetPreferences
	 */
	public function testOnGetPreferencesShowPreferencesEnabledSkinSectionMissingLatest() {
		$config = new HashConfig( [
			'VectorAdShowSkinPreferences' => true,
			// '2' is latest.
			'VectorAdDefaultSkinVersionForExistingAccounts' => '2',
			'VectorAdDefaultSidebarVisibleForAuthorisedUser' => true
		] );
		$this->setService( 'VectorAd.Config', $config );

		$prefs = [
			'foo' => [],
			'bar' => [],
		];
		Hooks::onGetPreferences( $this->getTestUser()->getUser(), $prefs );
		$this->assertSame(
			$prefs,
			[
				'foo' => [],
				'bar' => [],
				'VectorAdSkinVersion' => [
					'type' => 'toggle',
					'label-message' => 'prefs-vectorad-enable-vectorad-1-label',
					'help-message' => 'prefs-vectorad-enable-vectorad-1-help',
					'section' => SKIN_PREFS_SECTION,
					// '0' is disabled (which means latest).
					'default' => '0',
					'hide-if' => [ '!==', 'wpskin', 'vectorad' ]
				],
				'VectorAdSidebarVisible' => [
					'type' => 'api',
					'default' => true
				],
			],
			'Legacy skin version is disabled.'
		);
	}

	/**
	 * @covers ::onPreferencesFormPreSave
	 */
	public function testOnPreferencesFormPreSaveVectorAdEnabledLegacyNewPreference() {
		$formData = [
			'skin' => 'vectorad',
			// True is Legacy.
			'VectorAdSkinVersion' => true,
		];
		$form = $this->createMock( HTMLForm::class );
		$user = $this->createMock( \User::class );
		$user->expects( $this->once() )
			->method( 'setOption' )
			// '1' is Legacy.
			->with( 'VectorAdSkinVersion', '1' );
		$result = true;
		$oldPreferences = [];

		Hooks::onPreferencesFormPreSave( $formData, $form, $user, $result, $oldPreferences );
	}

	/**
	 * @covers ::onPreferencesFormPreSave
	 */
	public function testOnPreferencesFormPreSaveVectorAdEnabledLatestNewPreference() {
		$formData = [
			'skin' => 'vectorad',
			// False is latest.
			'VectorAdSkinVersion' => false,
		];
		$form = $this->createMock( HTMLForm::class );
		$user = $this->createMock( \User::class );
		$user->expects( $this->once() )
			->method( 'setOption' )
			// '2' is latest.
			->with( 'VectorAdSkinVersion', '2' );
		$result = true;
		$oldPreferences = [];

		Hooks::onPreferencesFormPreSave( $formData, $form, $user, $result, $oldPreferences );
	}

	/**
	 * @covers ::onPreferencesFormPreSave
	 */
	public function testOnPreferencesFormPreSaveVectorAdEnabledNoNewPreference() {
		$formData = [
			'skin' => 'vectorad',
		];
		$form = $this->createMock( HTMLForm::class );
		$user = $this->createMock( \User::class );
		$user->expects( $this->never() )
			->method( 'setOption' );
		$result = true;
		$oldPreferences = [];

		Hooks::onPreferencesFormPreSave( $formData, $form, $user, $result, $oldPreferences );
	}

	/**
	 * @covers ::onPreferencesFormPreSave
	 */
	public function testOnPreferencesFormPreSaveVectorAdDisabledNoOldPreference() {
		$formData = [
			// False is latest.
			'VectorAdSkinVersion' => false,
		];
		$form = $this->createMock( HTMLForm::class );
		$user = $this->createMock( \User::class );
		$user->expects( $this->never() )
			->method( 'setOption' );
		$result = true;
		$oldPreferences = [];

		Hooks::onPreferencesFormPreSave( $formData, $form, $user, $result, $oldPreferences );
	}

	/**
	 * @covers ::onPreferencesFormPreSave
	 */
	public function testOnPreferencesFormPreSaveVectorAdDisabledOldPreference() {
		$formData = [
			// False is latest.
			'VectorAdSkinVersion' => false,
		];
		$form = $this->createMock( HTMLForm::class );
		$user = $this->createMock( \User::class );
		$user->expects( $this->once() )
			->method( 'setOption' )
			->with( 'VectorAdSkinVersion', 'old' );
		$result = true;
		$oldPreferences = [
			'VectorAdSkinVersion' => 'old',
		];

		Hooks::onPreferencesFormPreSave( $formData, $form, $user, $result, $oldPreferences );
	}

	/**
	 * @covers ::onLocalUserCreated
	 */
	public function testOnLocalUserCreatedLegacy() {
		$config = new HashConfig( [
			// '1' is Legacy.
			'VectorAdDefaultSkinVersionForNewAccounts' => '1',
		] );
		$this->setService( 'VectorAd.Config', $config );

		$user = $this->createMock( \User::class );
		$user->expects( $this->once() )
		->method( 'setOption' )
			// '1' is Legacy.
			->with( 'VectorAdSkinVersion', '1' );
		$isAutoCreated = false;
		Hooks::onLocalUserCreated( $user, $isAutoCreated );
	}

	/**
	 * @covers ::onLocalUserCreated
	 */
	public function testOnLocalUserCreatedLatest() {
		$config = new HashConfig( [
			// '2' is latest.
			'VectorAdDefaultSkinVersionForNewAccounts' => '2',
		] );
		$this->setService( 'VectorAd.Config', $config );

		$user = $this->createMock( \User::class );
		$user->expects( $this->once() )
		->method( 'setOption' )
			// '2' is latest.
			->with( 'VectorAdSkinVersion', '2' );
		$isAutoCreated = false;
		Hooks::onLocalUserCreated( $user, $isAutoCreated );
	}

	/**
	 * @covers ::onSkinTemplateNavigation
	 */
	public function testOnSkinTemplateNavigation() {
		$this->setMwGlobals( [
			'wgVectorAdUseIconWatch' => true
		] );
		$skin = new SkinVectorAd();
		$contentNavWatch = [
			'actions' => [
				'watch' => [ 'class' => 'watch' ],
			]
		];
		$contentNavUnWatch = [
			'actions' => [
				'move' => [ 'class' => 'move' ],
				'unwatch' => [],
			],
		];

		Hooks::onSkinTemplateNavigation( $skin, $contentNavUnWatch );
		Hooks::onSkinTemplateNavigation( $skin, $contentNavWatch );

		$this->assertTrue(
			strpos( $contentNavWatch['views']['watch']['class'], 'icon' ) !== false,
			'Watch list items require an "icon" class'
		);
		$this->assertTrue(
			strpos( $contentNavUnWatch['views']['unwatch']['class'], 'icon' ) !== false,
			'Unwatch list items require an "icon" class'
		);
		$this->assertFalse(
			strpos( $contentNavUnWatch['actions']['move']['class'], 'icon' ) !== false,
			'List item other than watch or unwatch should not have an "icon" class'
		);
	}
}
