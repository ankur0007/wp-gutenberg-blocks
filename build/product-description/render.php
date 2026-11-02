<?php
$description = $block->context['avcl/description'] ?? '';

if ( $description !== '' ) {
    echo '<p ' . get_block_wrapper_attributes() . '>';
    echo wp_kses_post( $description );
    echo '</p>';
}
?>