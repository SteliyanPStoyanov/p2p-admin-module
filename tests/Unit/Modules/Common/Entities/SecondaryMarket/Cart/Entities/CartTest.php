<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use InvalidArgumentException;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollection;
use PHPUnit\Framework\TestCase;
use TypeError;

class CartTest extends TestCase
{
    public function testNewCart(): void
    {
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::new($investor, 'sell', $cartLoansCollection);

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart',
            $cart
        );
    }

    public function testCreateCart(): void
    {
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::create(1, $investor, 'sell', $cartLoansCollection);

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart',
            $cart
        );
    }

    public function testCreateNoCartId(): void
    {
        $this->expectException(TypeError::class);
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        Cart::create(null, $investor, 'sell', $cartLoansCollection);
    }

    public function testWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        Cart::create(1, $investor, 'Test', $cartLoansCollection);
    }

    public function testGetCartId(): void
    {
        $expectedCartId = 3;
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::create($expectedCartId, $investor, 'sell', $cartLoansCollection);

        $this->assertEquals(
            $expectedCartId,
            $cart->getCartId()
        );
    }

    public function testGetInvestor(): void
    {
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::new($investor, 'sell', $cartLoansCollection);

        $this->assertInstanceOf(
            'Modules\Common\Entities\Investor',
            $cart->getInvestor()
        );
    }

    public function testGetType(): void
    {
        $expectedType = 'sell';
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::create(3, $investor, $expectedType, $cartLoansCollection);

        $this->assertEquals(
            $expectedType,
            $cart->getType()
        );
    }

    public function testGetLoans(): void
    {
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::new($investor, 'sell', $cartLoansCollection);

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollection',
            $cart->getLoans()
        );
    }

    public function testGetAsArray(): void
    {
        $investor = $this->createMock(Investor::class);
        $cartLoansCollection = $this->createMock(CartLoansCollection::class);
        $cart = Cart::new($investor, 'sell', $cartLoansCollection);

        $this->assertIsArray($cart->getAsArray());
    }
}
