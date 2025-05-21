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

class Option implements \JsonSerializable
{
    /** @var ?string */
    private $colorBorder;
    /** @var ?string */
    private $colorBorderSelected;
    /** @var ?string */
    private $colorDetails;
    /** @var ?string */
    private $colorText;
    /** @var ?string */
    private $radiusBorder;

    public function getColorBorder(): ?string
    {
        return $this->colorBorder;
    }

    public function getColorBorderSelected(): ?string
    {
        return $this->colorBorderSelected;
    }

    public function getColorDetails(): ?string
    {
        return $this->colorDetails;
    }

    public function getColorText(): ?string
    {
        return $this->colorText;
    }

    public function getRadiusBorder(): ?string
    {
        return $this->radiusBorder;
    }

    /**
     * @param string|null $colorBorder
     *
     * @maps color_border
     */
    public function setColorBorder(?string $colorBorder): void
    {
        $this->colorBorder = $colorBorder;
    }

    /**
     * @maps color_border_selected
     */
    public function setColorBorderSelected(?string $colorBorderSelected): void
    {
        $this->colorBorderSelected = $colorBorderSelected;
    }

    /**
     * @maps color_details
     */
    public function setColorDetails(?string $colorDetails): void
    {
        $this->colorDetails = $colorDetails;
    }

    /**
     * @maps color_text
     */
    public function setColorText(?string $colorText): void
    {
        $this->colorText = $colorText;
    }

    /**
     * @maps radius_border
     */
    public function setRadiusBorder(?string $radiusBorder): void
    {
        $this->radiusBorder = $radiusBorder;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['color_border'] = $this->getColorBorder();
        $json['color_border_selected'] = $this->getColorBorderSelected();
        $json['color_details'] = $this->getColorDetails();
        $json['color_text'] = $this->getColorText();
        $json['radius_border'] = $this->getRadiusBorder();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
