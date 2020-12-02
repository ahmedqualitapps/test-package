<?php
/**
 * CheckoutSuccess observer for checkout_onepage_controller_success_action event
 * php version PHP 7.4.11
 * 
 * @category Extension
 * @package  VendfyForMagento
 * @author   Vendfy Developer <dev@vendfy.com>
 * @license  GPLv2 or other
 * @link     https://vendfy.com/
 */
namespace Vendfy\VendfyForMagento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

/**
 * CheckoutSuccess Class
 * 
 * @category Extension
 * @package  VendfyForMagento
 * @author   Vendfy Developer <dev@vendfy.com>
 * @license  GPLv2 or other
 * @link     https://vendfy.com/
 */
class CheckoutSuccess implements ObserverInterface
{
    /**
     * OrderModel
     * 
     * @var OrderFactory
     */
    protected $orderModel;

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    private $_cookieManager;

    /**
     * Contstructor
     * 
     * @param OrderFactory           $orderModel    OrderModel
     * @param CookieManagerInterface $cookieManager CookieManager
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        OrderFactory $orderModel,
        CookieManagerInterface $cookieManager
    ) {
        $this->orderModel = $orderModel;
        $this->_cookieManager = $cookieManager;
    }

    /**
     * VendfyForMagento checkout_onepage_controller_success_action observer
     * 
     * @param Observer $observer Observer
     * 
     * @return void
     * @throws InputException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();

        if (count($orderIds)) {
            if (!isset($_COOKIE['ref'])) {
                return;
            }

            $orderId = $orderIds[0];
            $order = $this->orderModel->create()->load($orderId);
            $refId = $_COOKIE['ref'];
            $total = $order['base_grand_total'];
            $discount = abs($order['base_discount_amount']);

            $data = "{$orderId}_{$total}_{$discount}";
            $url = "http://localhost:5000/{$refId}/{$data}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_exec($ch);
            curl_close($ch);

            // remove ref cookie
            $metadata = new CookieMetadata(['path' => '/']);
            $this->_cookieManager->deleteCookie('ref', $metadata);
        }
    }
}
