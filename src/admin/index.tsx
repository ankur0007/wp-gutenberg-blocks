import { createRoot } from '@wordpress/element';
import ShopifySettings from './shopifySettings';
import './style.scss';

const mount = document.getElementById( 'avcl-blocks-settings' );

if ( mount ) {
	createRoot( mount ).render( <ShopifySettings /> );
}
