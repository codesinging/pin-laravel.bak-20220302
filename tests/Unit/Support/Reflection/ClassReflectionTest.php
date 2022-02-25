<?php

namespace Tests\Unit\Support\Reflection;

use App\Support\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @title 类反射测试
 *
 * @author esinger
 */
class ClassReflectionTest extends TestCase
{
    protected string $file = ClassReflectionTest::class;

    protected ClassReflection $reflection;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->reflection = new ClassReflection($this->file);
    }

    public function testClassTitle()
    {
        $title = $this->reflection->classTitle();

        self::assertEquals('类反射测试', $title);
    }

    /**
     * @title 测试方法标题
     * @return void
     * @throws ReflectionException
     */
    public function testMethodTitle()
    {
        self::assertNull($this->reflection->methodTitle('testClassTitle'));
        self::assertEquals('测试方法标题', $this->reflection->methodTitle('testMethodTitle'));
    }
}
