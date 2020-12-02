<?php
/**
 * VendfyForMagento
 * php version PHP 7.4.11
 * 
 * @category Extension
 * @package  VendfyForMagento
 * @author   Vendfy Developer <dev@vendfy.com>
 * @license  GPLv2 or other
 * @link     https://vendfy.com/
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Vendfy_VendfyForMagento',
    __DIR__
);
