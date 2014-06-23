<?php

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use TreeHouse\Model\Config\Config;
use TreeHouse\Model\Config\ConfigBuilder;
use TreeHouse\Model\Config\Tests\Field\Foo;
use TreeHouse\Model\Config\Tests\Field\MultiFoo;
use TreeHouse\Model\Config\Validator\Constraints\ConfigValue;
use TreeHouse\Model\Config\Validator\Constraints\ConfigValueValidator;

class ConfigValueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var ConfigValueValidator
     */
    protected $validator;

    protected function setUp()
    {
        $this->config = $this->getConfigStub();

        $this->context = $this->getExecutionContextMock();
        $this->validator = new ConfigValueValidator($this->config);
        $this->validator->initialize($this->context);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\InvalidArgumentException
     */
    public function testInvalidConfigField()
    {
        $constraint = new ConfigValue(['name' => 'wut']);
        $this->validator->validate(1, $constraint);
    }

    public function testNullValue()
    {
        $this->context
            ->expects($this->never())
            ->method('addViolation')
        ;

        $this->validator->validate(null, new ConfigValue(['name' => 'foo']));
    }

    public function testSingleValueForMultiValuedConfig()
    {
        $constraint = new ConfigValue(['name' => 'fooz']);

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->arrayMessage)
        ;

        $this->validator->validate(1, $constraint);
    }

    public function testValidFieldValue()
    {
        $this->context->expects($this->never())->method('addViolation');
        $this->validator->validate(Foo::FOO, new ConfigValue(['name' => 'foo']));
    }

    public function testValidNumericStringFieldValue()
    {
        $this->context->expects($this->never())->method('addViolation');
        $this->validator->validate((string) Foo::FOO, new ConfigValue(['name' => 'foo']));
    }

    public function testInvalidNumericStringFieldValue()
    {
        $constraint = new ConfigValue(['name' => 'foo']);

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->numericMessage)
        ;

        $this->validator->validate('string', $constraint);
    }

    public function testInvalidFieldValue()
    {
        $constraint = new ConfigValue(['name' => 'foo']);

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message)
        ;

        $this->validator->validate(Foo::BAR + 1000, $constraint);
    }

    public function testMultipleValidFieldValues()
    {
        $constraint = new ConfigValue(['name' => 'fooz']);
        $values = [
            MultiFoo::FOOZ,
            MultiFoo::BAZZ
        ];

        $this->context->expects($this->never())->method('addViolation');
        $this->validator->validate($values, $constraint);
    }

    public function testInvalidFieldValueInMultipleValues()
    {
        $constraint = new ConfigValue(['name' => 'fooz']);
        $values = [
            MultiFoo::FOOZ,
            MultiFoo::BARZ * 100,
            MultiFoo::BAZZ,
        ];

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message)
        ;

        $this->validator->validate($values, $constraint);
    }

    /**
     * @return ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExecutionContextMock()
    {
        return $this
            ->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }

    /**
     * @return Config
     */
    private function getConfigStub()
    {
        $builder = new ConfigBuilder();
        $builder->addField('foo', Foo::class);
        $builder->addField('fooz', MultiFoo::class);

        return $builder->getConfig();
    }
}