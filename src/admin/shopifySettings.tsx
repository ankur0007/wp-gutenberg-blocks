import { Button, TextControl, TabPanel, Notice } from '@wordpress/components';

import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import { useState } from 'react';
import apiFetch from '@wordpress/api-fetch';

interface CustomTab {
	name: string;
	title: string;
}

function isCustomTab( value: unknown ): value is CustomTab {
	return (
		typeof value === 'object' &&
		value !== null &&
		'name' in value &&
		typeof value.name === 'string' &&
		'title' in value &&
		typeof value.title === 'string'
	);
}

export default function ShopifySettings(): JSX.Element {
	const defaultTabs: CustomTab[] = [
		{
			name: 'avcl_blocks_settings',
			title: __( 'Shopify ASIN Settings', 'avcl-blocks' ),
		},
	];

	const saveCredentials = async (
		clientId: string,
		clientSecret: string,
		shopDomain: string
	) => {
		setError( '' );
		setNotice( '' );

		if ( ! clientId.trim() ) {
			setError( 'Enter a valid client ID' );
			return;
		}
		if ( ! clientSecret.trim() ) {
			setError( 'Enter a valid client secret' );
			return;
		}
		if ( ! /^[a-z0-9]+(?:-[a-z0-9]+)*$/i.test( shopDomain.trim() ) ) {
			setError( 'Enter a valid shop domain' );
			return;
		}

		const url = '/avcl-blocks/v1/settings/save-access';
		try {
			const access = await apiFetch( {
				path: url,
				method: 'POST',
				data: { clientId, clientSecret, shopDomain },
			} );
			setNotice( 'Credentials have been verified successfully!' );
			setConnected( true );
		} catch ( error: unknown ) {
			if (
				typeof error === 'object' &&
				error !== null &&
				'message' in error &&
				typeof error.message === 'string'
			) {
				setError( error.message );
			} else {
				setError( 'Something went wrong. Please try again.' );
			}
		}
	};

	const filtered: unknown = applyFilters( 'avcl.settings', defaultTabs );

	if ( ! Array.isArray( filtered ) || ! filtered.every( isCustomTab ) ) {
		throw new TypeError(
			'The avcl.settings filter returned invalid tabs.'
		);
	}

	const availableTabs: CustomTab[] = filtered;

	const [ clientId, setClientId ] = useState( '' );
	const [ clientSecret, setClientSecret ] = useState( '' );
	const [ shopDomain, setShopDomain ] = useState( '' );
	const [ error, setError ] = useState( '' );
	const [ notice, setNotice ] = useState( '' );
	const [ isConnected, setConnected ] = useState( false );

	return (
		<div className="avcl-settings">
			<header className="avcl-settings__header">
				<h1>{ __( 'AVCL Blocks Settings', 'avcl-blocks' ) }</h1>
				<p>
					{ __(
						'Configure the Shopify Product Advertising API connection.',
						'avcl-blocks'
					) }
				</p>
			</header>
			<TabPanel
				className="avcl-settings__tabs"
				initialTabName="avcl_blocks_settings"
				tabs={ availableTabs }
			>
				{ ( activeTab ) => {
					if ( activeTab.name === 'avcl_blocks_settings' ) {
						return (
							<section className="avcl-settings__panel">
								<div className="avcl-settings__section-heading">
									<h2>
										{ __( 'Shopify API', 'avcl-blocks' ) }
									</h2>
									<p>
										{ __(
											'Enter your Shopify partner tag to connect product lookups.',
											'avcl-blocks'
										) }
									</p>
								</div>
								{ error && (
									<Notice
										status="error"
										isDismissible={ false }
									>
										{ error }
									</Notice>
								) }
								{ notice && (
									<Notice
										status="success"
										isDismissible={ true }
									>
										{ notice }
									</Notice>
								) }
								<TextControl
									className="avcl-settings__shop"
									label={ __( 'Shop Name', 'avcl-blocks' ) }
									value={ shopDomain }
									onChange={ setShopDomain }
								/>
								<TextControl
									className="avcl-settings__client_id"
									label={ __( 'Client ID', 'avcl-blocks' ) }
									value={ clientId }
									onChange={ setClientId }
								/>
								<TextControl
									className="avcl-settings__client_secret"
									label={ __(
										'Client Secret',
										'avcl-blocks'
									) }
									value={ clientSecret }
									onChange={ setClientSecret }
								/>
								<div className="avcl-settings__actions">
									{ ! isConnected && (
										<Button
											variant="primary"
											onClick={ () => {
												saveCredentials(
													clientId,
													clientSecret,
													shopDomain
												);
											} }
										>
											{ __(
												'Save settings',
												'avcl-blocks'
											) }
										</Button>
									) }
									{ isConnected && (
										<>
											<Button
												variant="primary"
												onClick={ () => {
													//verifyCredentials(clientId, clientSecret, shopDomain);
												} }
											>
												{ __(
													'Check Connection',
													'avcl-blocks'
												) }
											</Button>
											<Button
												variant="secondary"
												onClick={ () => {
													//verifyCredentials(clientId, clientSecret, shopDomain);
												} }
											>
												{ __(
													'Remove Connection',
													'avcl-blocks'
												) }
											</Button>
										</>
									) }
								</div>
							</section>
						);
					}
					return null;
				} }
			</TabPanel>
		</div>
	);
}
