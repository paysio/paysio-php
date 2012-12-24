<?php

namespace Paysio;

class Currency
{
    const TYPE_RUB = 'rur',
          TYPE_USD = 'usd',
          TYPE_EUR = 'eur';

    /**
     * Default currency settings
     * @var array
     */
    protected static $_defaults = array(
        self::TYPE_RUB => array(
            'precision' => 2
        ),
        self::TYPE_EUR => array(
            'precision' => 2
        ),
        self::TYPE_USD => array(
            'precision' => 2
        ),
    );

    /**
     * @static
     * @param $currencyId
     * @return int
     * @throws \RuntimeException
     */
    public static function getPrecision($currencyId)
    {
        if (!isset(self::$_defaults[$currencyId])) {
            throw new \RuntimeException('Unsupported currency');
        }
        return self::$_defaults[$currencyId]['precision'];
    }

    /**
     * @static
     * @param $amount
     * @param null $currencyId
     * @return float
     */
    public static function decimalAmount($amount, $currencyId = null)
    {
        if ($currencyId === null) {
            $currencyId = Api::getCurrency();
        }
        $precision = self::getPrecision($currencyId);
        return $amount / pow(10, $precision);
    }

    /**
     * @static
     * @param $amount
     * @param null $currencyId
     * @return int
     */
    public static function normalizeAmount($amount, $currencyId = null)
    {
        if ($currencyId === null) {
            $currencyId = Api::getCurrency();
        }
        $precision = self::getPrecision($currencyId);
        return $amount * pow(10, $precision);
    }
}