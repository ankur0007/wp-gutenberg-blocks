<?php
use AVCL\Blocks\Currencies;

$price =  $block->context['avcl/price'] ?? 0;
$currency_code =  $block->context['avcl/currency'] ?? 0;
$currency = Currencies::get_currency($currency_code);


if($price > 0){
   echo '<p ' . get_block_wrapper_attributes() . '>'.sprintf('%s %d', $currency['symbol'], $price).'</p>'; 
}