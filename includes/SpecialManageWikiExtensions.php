<?php
class SpecialManageWikiExtensions extends SpecialPage {
	function __construct() {
		parent::__construct( 'ManageWikiExtensions', 'managewiki' );
	}

	function execute( $par ) {
		global $wgEnableManageWiki, $wgManageWikiHelpUrl, $wgManageWikiExtensions, $wgCreateWikiGlobalWiki, $wgDBname;

		$out = $this->getOutput();
		$this->setHeaders();
		if ( $wgManageWikiHelpUrl ) {
			$this->getOutput()->addHelpLink( $wgManageWikiHelpUrl, true );
		}

		if ( !$wgEnableManageWiki ) {
			$out->addWikiMsg( 'managewiki-disabled' );
			return false;
		}

		if ( !$wgManageWikiExtensions ) {
			$out->addWikiMsg( 'managewiki-extensions-disabled' );
			return false;
		}

		$this->checkPermissions();

		if ( $wgCreateWikiGlobalWiki !== $wgDBname ) {
			$this->showWikiForm( $wgDBname );
		} elseif ( !is_null( $par ) && $par !== '' ) {
			$this->showWikiForm( $par );
		} else {
			$this->showInputBox();
		}
	}

	function showInputBox() {
		$formDescriptor = array(
			'dbname' => array(
				'label-message' => 'managewiki-label-dbname',
				'type' => 'text',
				'size' => 20,
				'required' => true,
				'name' => 'mwDBname',
			)
		);

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext(), 'searchForm' );
		$htmlForm->setMethod( 'post' )
			->setSubmitCallback( array( $this, 'onSubmitRedirectToWikiForm' ) )
			->prepareForm()
			->show();

		return true;
	}

	function onSubmitRedirectToWikiForm( array $params ) {
		global $wgRequest;

		if ( $params['dbname'] !== '' ) {
			header( 'Location: ' . SpecialPage::getTitleFor( 'ManageWikiExtensions' )->getFullUrl() . '/' . $params['dbname'] );
		} else {
			return 'Invalid url.';
		}

		return true;
	}

	function showWikiForm( $wiki ) {
		global $wgManageWikiExtensions, $wgUser;

		$out = $this->getOutput();

		$dbName = $wiki;

		if ( $wiki == NULL ) {
			$out->addHTML( '<div class="errorbox">' . wfMessage( 'managewiki-missing' )->escaped() . '</div>' );
			return false;
		}

		if ( !$this->getRequest()->wasPosted() ) {
			$out->addWikiMsg( 'managewiki-extensions-header', $dbName );
		}

		$formFactory = new ManageWikiFormFactory();
		$htmlForm = $formFactory->getForm( $dbName, $this->getContext(), 'extensions' );
		$sectionTitles = $htmlForm->getFormSections();

		$sectTabs = [];
		foreach( $sectionTitles as $key ) {
			$sectTabs[] = [
				'name' => $key,
				'label' => $htmlForm->getLegend( $key )
			];
		}

		$out->addJsConfigVars( 'wgManageWikiBaseFormTabs', $sectTabs );

		$htmlForm->show();
	}

	protected function getGroupName() {
		return 'wikimanage';
	}
}
