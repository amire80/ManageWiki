<?php

namespace Miraheze\ManageWiki\Hooks;

use MediaWiki\HookContainer\HookContainer;

class ManageWikiHookRunner implements
	ManageWikiCoreAddFormFieldsHook,
	ManageWikiCoreFormSubmissionHook
{
	/**
	 * @var HookContainer
	 */
	private $container;

	/**
	 * @param HookContainer $container
	 */
	public function __construct( HookContainer $container ) {
		$this->container = $container;
	}

	/** @inheritDoc */
	public function onManageWikiCoreAddFormFields( $ceMW, $context, $dbName, &$formDescriptor ): void {
		$this->container->run(
			'ManageWikiCoreAddFormFields',
			[ &$formDescriptor ]
		);
	}

	/** @inheritDoc */
	public function onManageWikiCoreFormSubmission( $context, $dbName, $dbw, $formData ): void {
		$this->container->run(
			'ManageWikiCoreFormSubmission',
			[ $dbName, $dbw, $formData ]
		);
	}
}
