<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 12.08.2016
 * Time: 18:53
 */

namespace ConfigOfProjectTests\Unit;


use ConfigOfProjectTests\PHPUnitHelper;

/**
 * Тестирование генератора
 *
 * Class ConfigTemplateGeneratorTest
 */
class ConfigTemplateGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPath()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject | \ConfigOfProject\ConfigTemplateGenerator $generator */
        $generator = $this->getMockBuilder(\ConfigOfProject\ConfigTemplateGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();
        $result = PHPUnitHelper::callProtectedMethod($generator, 'getPath',[]);
        $this->assertEquals(1, preg_match('#\/\.\.\/\.\.\/\.\.\/\.\.\/\.\.\/config\/template#ui', $result));
    }

    public function testGenerate()
    {
        
    }
}
