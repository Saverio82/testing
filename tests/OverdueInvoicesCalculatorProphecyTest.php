<?php

declare(strict_types=1);


namespace tests;

use Invoice;
use InvoiceInMemoryRepository;
use Money\Money;
use OverdueInvoicesCalculator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OverdueInvoicesCalculatorProphecyTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_sums_all_overdue_invoices()
    {
        $requestDate = new \DateTime();

        $invoice1ToPayAmount = Money::EUR(10);
        $invoice1 = new Invoice($invoice1ToPayAmount, (clone $requestDate)->modify('-1 day'));

        $invoice2 = new Invoice(Money::EUR(50), $requestDate);

        $invoice3ToPayAmount = Money::EUR(25);
        $invoice3 = new Invoice($invoice3ToPayAmount, (clone $requestDate)->modify('-1 day'));

        $repo = $this->prophesize(InvoiceInMemoryRepository::class);
        $repo->findAll()
            ->willReturn([$invoice1, $invoice2, $invoice3]);

        $calculator = new OverdueInvoicesCalculator($repo->reveal());

        $this->assertEquals($invoice1ToPayAmount->add($invoice3ToPayAmount), $calculator->getAmountDue($requestDate));
    }
}