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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

/** Keys are based on official standard. @see https://datatracker.ietf.org/doc/html/rfc7517 */
class JWK implements \JsonSerializable
{
    /** @var string */
    private $use;
    /** @var string */
    private $kty;
    /** @var string */
    private $kid;
    /** @var string */
    private $alg;
    /** @var string */
    private $n;
    /** @var string */
    private $e;

    /**
     * @return string
     */
    public function getUse(): string
    {
        return $this->use;
    }

    /**
     * @maps use
     */
    public function setUse(string $use): void
    {
        $this->use = $use;
    }

    /**
     * @return string
     */
    public function getKty(): string
    {
        return $this->kty;
    }

    /**
     * @maps kty
     */
    public function setKty(string $kty): void
    {
        $this->kty = $kty;
    }

    /**
     * @return string
     */
    public function getKid(): string
    {
        return $this->kid;
    }

    /**
     * @maps kid
     */
    public function setKid(string $kid): void
    {
        $this->kid = $kid;
    }

    /**
     * @return string
     */
    public function getAlg(): string
    {
        return $this->alg;
    }

    /**
     * @maps alg
     */
    public function setAlg(string $alg): void
    {
        $this->alg = $alg;
    }

    /**
     * @return string
     */
    public function getN(): string
    {
        return $this->n;
    }

    /**
     * @maps n
     */
    public function setN(string $n): void
    {
        $this->n = $n;
    }

    /**
     * @return string
     */
    public function getE(): string
    {
        return $this->e;
    }

    /**
     * @maps e
     */
    public function setE(string $e): void
    {
        $this->e = $e;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['use'] = $this->getUse();
        $json['kty'] = $this->getKty();
        $json['kid'] = $this->getKid();
        $json['alg'] = $this->getAlg();
        $json['n'] = $this->getN();
        $json['e'] = $this->getE();

        return array_filter($json, static function ($val) {
            return !empty($val);
        });
    }
}
