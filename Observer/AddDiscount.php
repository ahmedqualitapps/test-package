<?php
/**
 * AddDiscount observer for checkout_cart_save_after event
 * php version PHP 7.4.11
 *
 * @category Extension
 * @package  VendfyForMagento
 * @author   Vendfy Developer <dev@vendfy.com>
 * @license  GPLv2 or other
 * @link     https://vendfy.com/
 */
namespace Vendfy\VendfyForMagento\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

/**
 * AddDiscount Class
 * 
 * @category Extension
 * @package  VendfyForMagento
 * @author   Vendfy Developer <dev@vendfy.com>
 * @license  GPLv2 or other
 * @link     https://vendfy.com/
 */
class AddDiscount implements ObserverInterface
{
    /**
     * CheckoutSession
     *
     * @var Session
     */
    private $_checkoutSession;

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    private $_cookieManager;

    /**
     * Contstructor
     * 
     * @param Session                $checkoutSession CheckoutSession
     * @param CookieManagerInterface $cookieManager   CookieManager
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $checkoutSession,
        CookieManagerInterface $cookieManager
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_cookieManager = $cookieManager;
    }

    /**
     * VendfyForMagento checkout_cart_save_after observer
     *
     * @param Observer $observer Observer
     * 
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        if (!isset($_COOKIE['couponCode'])) {
            return;
        }

        $code = $_COOKIE['couponCode'];

        $this->_checkoutSession
            ->getQuote()
            ->setCouponCode($code)
            ->collectTotals()
            ->save();

        // remove couponCode cookie
        $metadata = new CookieMetadata(['path' => '/']);
        $this->_cookieManager->deleteCookie('couponCode', $metadata);
    }
}
