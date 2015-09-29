<?php

namespace TreeHouse\Model\Config\Tests\Field;

use TreeHouse\Model\Config\Field\Enum;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $foo = new Foo(Foo::BAR);

        $this->assertInstanceOf(Enum::class, $foo);
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function it_throws_an_exception_when_constructed_with_invalid_value()
    {
        new Foo('bar');
    }

    /**
     * @test
     */
    public function it_returns_the_name()
    {
        $foo = new Foo(Foo::BAR);

        $this->assertSame('BAR', $foo->getName());
    }

    /**
     * @test
     */
    public function it_returns_the_value()
    {
        $foo = new Foo(Foo::BAR);

        $this->assertSame(Foo::BAR, $foo->getValue());
    }

    /**
     * @test
     */
    public function it_can_be_cast_to_string()
    {
        $foo = new Foo(Foo::BAR);

        $this->assertEquals($foo->getValue(), (string) $foo);
    }

    /**
     * @test
     */
    public function it_can_return_its_values()
    {
        $this->assertEquals(
            [
                'FOO' => Foo::FOO,
                'BAR' => Foo::BAR,
                'BAZ' => Foo::BAZ,
            ],
            Foo::toArray()
        );
    }

    /**
     * @test
     */
    public function it_can_be_multivalued()
    {
        $this->assertFalse(Foo::isMultiValued());
        $this->assertTrue(MultiFoo::isMultiValued());
    }

}
