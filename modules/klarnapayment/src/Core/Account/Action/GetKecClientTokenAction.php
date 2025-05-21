<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Core\Account\Action;

use KlarnaPayment\Module\Core\Account\Exception\CouldNotProcessExpressCheckout;
use KlarnaPayment\Module\Core\Account\Processor\CanProcessExpressCheckout;
use KlarnaPayment\Module\Core\Account\Provider\AddressChecksumProvider;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaExpressCheckoutRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetKecClientTokenAction
{
    /**
     * @var AddressChecksumProvider
     */
    private $addressChecksumProvider;
    /** @var CanProcessExpressCheckout */
    private $canProcessExpressCheckout;
    /** @var KlarnaExpressCheckoutRepositoryInterface */
    private $klarnaExpressCheckoutRepository;
    /** @var ValidateKecAddressAction */
    private $validateKecAddressAction;

    public function __construct(
        AddressChecksumProvider $addressChecksumProvider,
        CanProcessExpressCheckout $canProcessExpressCheckout,
        KlarnaExpressCheckoutRepositoryInterface $klarnaExpressCheckoutRepository,
        ValidateKecAddressAction $validateKecAddressAction
    ) {
        $this->addressChecksumProvider = $addressChecksumProvider;
        $this->canProcessExpressCheckout = $canProcessExpressCheckout;
        $this->klarnaExpressCheckoutRepository = $klarnaExpressCheckoutRepository;
        $this->validateKecAddressAction = $validateKecAddressAction;
    }

    public function run(int $cartId, int $invoiceAddressId): string
    {
        /** @var \KlarnaExpressCheckout|null $klarnaExpressCheckout */
        $klarnaExpressCheckout = $this->klarnaExpressCheckoutRepository->findOneBy(['id_cart' => $cartId]);

        $this->canProcessExpressCheckout->run($klarnaExpressCheckout);

        $cart = new \Cart($cartId);

        if (!$cart->id_address_invoice) {
            return $klarnaExpressCheckout->client_token;
        }

        $address = new \Address($invoiceAddressId);

        // NOTE: KEC prefills fields and we need compare them only then all fields are filled
        // NOTE: This is needed because how OPC modules working.
        if ($this->validateKecAddressAction->run($address)) {
            $cartAddressChecksum = $this->addressChecksumProvider->get([
                'city' => $address->city,
                'country' => $address->id_country,
                'family_name' => $address->lastname,
                'given_name' => $address->firstname,
                'phone' => $address->phone,
                'postal_code' => $address->postcode,
                'street_address' => $address->address1,
                'street_address2' => $address->address2,
                'region' => (int) $address->id_state,
            ]);

            if ($cartAddressChecksum !== $klarnaExpressCheckout->address_checksum) {
                $klarnaExpressCheckout->delete();

                throw CouldNotProcessExpressCheckout::addressMismatch();
            }
        }

        return $klarnaExpressCheckout->client_token;
    }
}
