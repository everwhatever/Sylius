<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Taxation\Checker;

use DateTimeInterface;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class TaxRateDateChecker implements TaxRateDateCheckerInterface
{
    public function __construct(
        protected DateTimeProviderInterface $calendar,
    ) {
    }

    public function filter(array $taxRates): ?TaxRateInterface
    {
        $now = $this->calendar->now();

        /** @var TaxRateInterface $taxRate */
        foreach ($taxRates as $key => $taxRate) {
            if (!$this->isInDate($now, $taxRate->getStartDate(), $taxRate->getEndDate())) {
                unset($taxRates[$key]);
            }
        }

        $taxRates = array_values($taxRates);

        return $taxRates[0] ?? null;
    }

    public function isInDate(DateTimeInterface $date, ?DateTimeInterface $startDate, ?DateTimeInterface $endDate): bool
    {
        if (null === $endDate) {
            return $startDate <= $date;
        }

        return $startDate <= $date && $endDate >= $date;
    }
}