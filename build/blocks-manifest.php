<?php
// This file is generated. Do not modify it manually.
return array(
	'hero-section' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'avcl-blocks/hero-section',
		'version' => '0.1.0',
		'title' => 'Hero Section',
		'category' => 'design',
		'attributes' => array(
			
		),
		'icon' => 'cover-image',
		'description' => 'A starter block for building a hero section.',
		'textdomain' => 'avcl-blocks',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'product-card' => array(
		'apiVersion' => 3,
		'name' => 'avcl/product-card',
		'title' => 'Product Card',
		'category' => 'widgets',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'allowedBlocks' => array(
			'core/image',
			'core/group',
			'avcl/product-title',
			'avcl/product-price',
			'avcl/product-description',
			'core/buttons'
		),
		'supports' => array(
			'html' => false
		),
		'attributes' => array(
			'productName' => array(
				'type' => 'string',
				'default' => 'Headphones'
			),
			'price' => array(
				'type' => 'number',
				'default' => 99
			),
			'imageUrl' => array(
				'type' => 'string',
				'default' => 'https://via.placeholder.com/300x200'
			),
			'position' => array(
				'type' => 'string',
				'default' => 'left'
			),
			'description' => array(
				'type' => 'string',
				'default' => 'This is a great product that you will love!'
			),
			'cardWidth' => array(
				'type' => 'number',
				'default' => 512
			),
			'currency' => array(
				'type' => 'string',
				'default' => '$'
			),
			'productUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'platform' => array(
				'type' => 'string',
				'default' => 'shopify'
			)
		),
		'providesContext' => array(
			'avcl/productName' => 'productName',
			'avcl/price' => 'price',
			'avcl/description' => 'description',
			'avcl/currency' => 'currency'
		)
	),
	'product-description' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'avcl/product-description',
		'title' => 'Product Description',
		'category' => 'widgets',
		'attributes' => array(
			
		),
		'textdomain' => 'avcl-blocks',
		'ancestor' => array(
			'avcl/product-card'
		),
		'editorScript' => 'file:./index.js',
		'usesContext' => array(
			'avcl/description'
		),
		'render' => 'file:./render.php',
		'supports' => array(
			'typography' => array(
				'fontSize' => true
			)
		)
	),
	'product-price' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'avcl/product-price',
		'title' => 'Product Price',
		'category' => 'widgets',
		'textdomain' => 'avcl-blocks',
		'ancestor' => array(
			'avcl/product-card'
		),
		'editorScript' => 'file:./index.js',
		'usesContext' => array(
			'avcl/price',
			'avcl/currency'
		),
		'supports' => array(
			'typoraphy' => array(
				'fontSize' => true
			)
		),
		'render' => 'file:./render.php'
	),
	'product-title' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'avcl/product-title',
		'title' => 'Product Title',
		'category' => 'widgets',
		'attributes' => array(
			
		),
		'textdomain' => 'avcl-blocks',
		'ancestor' => array(
			'avcl/product-card'
		),
		'editorScript' => 'file:./index.js',
		'usesContext' => array(
			'avcl/productName'
		),
		'render' => 'file:./render.php'
	),
	'related-posts' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/avcl-related-posts',
		'version' => '0.1.0',
		'title' => 'AVCL Related Posts',
		'category' => 'widgets',
		'icon' => 'smiley',
		'description' => 'Displays a configurable list of related posts.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'avcl-blocks',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js',
		'attributes' => array(
			'postsToShow' => array(
				'type' => 'number',
				'default' => 3
			),
			'postType' => array(
				'type' => 'string',
				'default' => 'post'
			),
			'taxonomy' => array(
				'type' => 'string',
				'default' => 'category'
			),
			'terms' => array(
				'type' => 'array',
				'default' => array(
					
				)
			),
			'order' => array(
				'type' => 'string',
				'default' => 'desc'
			),
			'orderby' => array(
				'type' => 'string',
				'default' => 'date'
			)
		),
		'render' => 'file:./render.php'
	)
);
