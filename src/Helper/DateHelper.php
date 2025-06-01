<?php
// src/Helper/DateHelper.php

namespace App\Helper;

class DateHelper
{
    /**
     * Форматирует дату с использованием IntlDateFormatter.
     *
     * @param \DateTimeInterface $date
     * @param string $pattern Паттерн форматирования (по умолчанию 'd MMMM y')
     * @param string $locale Локаль (по умолчанию 'ru_RU')
     * @param string $timezone Часовой пояс (по умолчанию 'Europe/Moscow')
     * @return string
     */
    public static function formatDate(
        ?\DateTimeInterface $date,
        string $pattern = 'd MMMM y',
        string $locale = 'ru_RU',
        string $timezone = 'Europe/Moscow'
    ): ?string {

        if (!$date) {
            return null;
        }

        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            $timezone,
            \IntlDateFormatter::GREGORIAN,
            $pattern
        );
        return $formatter->format($date);
    }
}