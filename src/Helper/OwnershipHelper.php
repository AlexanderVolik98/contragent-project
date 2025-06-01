<?php

namespace App\Helper;

use App\Entity\Founder;

class OwnershipHelper
{
    /**
     * Рассчитывает все доли так, чтобы сумма составляла 100%
     *
     * @param Founder[] $founders
     * @param int $precision
     * @return array<int, float> Индекс => доля в %
     */
    public static function calculateAllFractions(array $founders, int $precision = 2): ?array
    {
        $absoluteShares = [];
        $total = 0;

        // Вычисляем абсолютные значения для всех основателей
        foreach ($founders as $founder) {

            if ($founder->getShareType() === 'FRACTION') {
                $numerator = $founder->getShare()['numerator'];
                $denominator = $founder->getShare()['denominator'];

                if ($denominator > 0) {
                    $value = $numerator / $denominator;
                } else {
                    $value = 0;
                }
            } else {
                if (null === $founder->getShare()) {
                    return null;
                }
                // Если уже в процентах, то просто делим на 100
                $value = $founder->getShare()['value'] / 100;
            }

            $absoluteShares[$founder->getId()] = $value;
            $total += $value;
        }

        // Теперь пересчитываем в проценты от общего
        $percentageShares = [];
        foreach ($absoluteShares as $index => $value) {
            $percentage = ($total > 0) ? round(($value / $total) * 100, $precision) : 0;
            $percentageShares[$index] = $percentage;
        }

        return $percentageShares;
    }
}
