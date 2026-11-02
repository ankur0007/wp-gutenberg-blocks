<?php
namespace AVCL\Blocks;

class Currencies extends Init{
	

	public function __construct() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );
	}

	public static function get_currencies(){
		return apply_filters(
			'avcl_blocks_currencies',
			[
				'USD' => [ 'label' => 'US Dollar ($)', 'symbol' => '$' ],
				'EUR' => [ 'label' => 'Euro (€)', 'symbol' => '€' ],
			]
		);
	}

	public static function get_currency(string $key): array{
		if(empty($key)){
			return [];
		}
		$currencies = self::get_currencies();
		return $currencies[$key] ?? [];
	}

	public function enqueue() {
		$block = \WP_Block_Type_Registry::get_instance()
			->get_registered( 'avcl/product-price' );

		if ( ! $block || empty( $block->editor_script_handles ) ) {
			return;
		}

		$currencies = self::get_currencies();

		wp_add_inline_script(
			$block->editor_script_handles[0],
			'window.avclProductPriceCurrencies = ' . wp_json_encode( $currencies ) . ';',
			'before'
		);
	}

	
}
