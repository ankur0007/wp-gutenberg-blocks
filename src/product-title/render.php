<?php
$product_name = $block->context['avcl/productName'] ?? '';
echo '<p '.get_block_wrapper_attributes().'>'.$product_name.'</p>';