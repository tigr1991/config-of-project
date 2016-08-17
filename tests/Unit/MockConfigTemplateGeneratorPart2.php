<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 16.08.2016
 * Time: 20:08
 */

namespace ConfigOfProjectTests\Unit;


class MockConfigTemplateGeneratorPart2 extends \ConfigOfProject\ConfigTemplateGenerator
{
    /**
     * @return string
     */
    protected static function getPath()
    {
        return __DIR__ . '/tmp/template';
    }
}