<?php

if (! function_exists('format_quantity')) {
    /**
     * Formatea una cantidad para mostrarla sin decimales si es entera
     * y manteniendo los decimales útiles si es fraccionaria.
     *
     * 1.00  -> "1"
     * 3.00  -> "3"
     * 0.50  -> "0.5"
     * 1.75  -> "1.75"
     */
    function format_quantity(float|int|string $value): string
    {
        $number = (float) $value;

        if ($number == (int) $number) {
            return (string) (int) $number;
        }

        return rtrim(rtrim(number_format($number, 2), '0'), '.');
    }
}
