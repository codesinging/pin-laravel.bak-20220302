<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Reflection;

use ReflectionClass;
use ReflectionException;

class ClassReflection
{
    /**
     * @var string
     */
    protected string $file = '';

    /**
     * @var ReflectionClass
     */
    protected ReflectionClass $class;

    /**
     * @param string $file
     *
     * @throws ReflectionException
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->class = new ReflectionClass($this->file);
    }

    /**
     * 获取类标题
     *
     * @return string|null
     */
    public function classTitle(): ?string
    {
        $comment = $this->class->getDocComment();
        return $this->parseTitle($comment);
    }

    /**
     * @param string $name
     *
     * @return string|null
     * @throws ReflectionException
     */
    public function methodTitle(string $name): ?string
    {
        $method = $this->class->getMethod($name);
        $comment = $method->getDocComment();
        return $this->parseTitle($comment);
    }

    /**
     * 从注释中获取标题
     *
     * @param string $comment
     *
     * @return string|null
     */
    private function parseTitle(string $comment): ?string
    {
        if (preg_match('#\*\s*@title\s+(.+)\s*\n#', $comment, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
