<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 12.08.2016
 * Time: 18:53
 */

namespace ConfigOfProjectTests\Unit;


use ConfigOfProject\Exception;
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
        $generator = $this
            ->getMockBuilder(\ConfigOfProject\ConfigTemplateGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();
        $result = PHPUnitHelper::callProtectedMethod($generator, 'getPath', []);
        $this->assertEquals(1, preg_match('#\/\.\.\/\.\.\/\.\.\/\.\.\/\.\.\/config\/template#ui', $result));
    }

    public function testGenerate()
    {
        $event = $this
            ->getMockBuilder(\Composer\Script\Event::class)
            ->disableOriginalConstructor()
            ->setMethods(['_', 'getComposer'])
            ->getMock();

        $event
            ->expects($this->at(0))
            ->method('getComposer')
            ->willReturn($this->getComposer());

        \ConfigOfProjectTests\Unit\MockConfigTemplateGeneratorPart1::generate($event);
    }

    /**
     * @return \Composer\Composer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getComposer()
    {
        /** @var \Composer\Composer | \PHPUnit_Framework_MockObject_MockObject $composer */
        $composer = $this
            ->getMockBuilder(\Composer\Composer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPackage', 'getRepositoryManager'])
            ->getMock();

        $composer
            ->expects($this->at(0))
            ->method('getPackage')
            ->willReturn($this->getPackage());

        $composer
            ->expects($this->at(1))
            ->method('getRepositoryManager')
            ->willReturn($this->getRepositoryManager());

        return $composer;
    }

    /**
     * @return \Composer\Package\BasePackage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPackage()
    {
        /** @var \Composer\Package\BasePackage | \PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this
            ->getMockBuilder(\Composer\Package\BasePackage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getExtra'])
            ->getMock();

        $package
            ->expects($this->at(0))
            ->method('getExtra')
            ->willReturn($this->getExtra());

        return $package;
    }

    /**
     * @return array
     */
    protected function getExtra()
    {
        return [
            \ConfigOfProject\ConfigTemplateGenerator::NAME_OF_SECTION =>
                [
                    'data_current_project_1',
                    'data_current_project_2',
                    'data_current_project_3',
                ],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | \Composer\Repository\RepositoryManager
     */
    protected function getRepositoryManager()
    {
        /** @var \Composer\Repository\RepositoryManager | \PHPUnit_Framework_MockObject_MockObject $package */
        $repository_manager = $this
            ->getMockBuilder(\Composer\Repository\RepositoryManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepositories'])
            ->getMock();

        $repository_manager
            ->expects($this->at(0))
            ->method('getRepositories')
            ->willReturn($this->getRepositories());

        return $repository_manager;
    }

    /**
     * @return \Composer\Repository\ArrayRepository[]
     */
    protected function getRepositories()
    {
        /** @var \Composer\Repository\ArrayRepository[] | \PHPUnit_Framework_MockObject_MockObject[] $repositories */
        $repositories = [];

        $repositories[0] = $this
            ->getMockBuilder(\Composer\Repository\ArrayRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['count'])
            ->getMock();

        $repositories[1] = $this
            ->getMockBuilder(\Composer\Repository\ArrayRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['count', 'getPackages'])
            ->getMock();

        $repositories[0]
            ->expects($this->at(0))
            ->method('count')
            ->willReturn(0);

        $repositories[1]
            ->expects($this->at(0))
            ->method('count')
            ->willReturn(1);

        $repositories[1]
            ->expects($this->at(1))
            ->method('getPackages')
            ->willReturn($this->getPackages());

        return $repositories;
    }

    /**
     * @return \Composer\Package\BasePackage[] |\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected function getPackages()
    {
        /** @var \Composer\Package\BasePackage | \PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this
            ->getMockBuilder(\Composer\Package\BasePackage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getExtra'])
            ->getMock();

        $package
            ->expects($this->at(0))
            ->method('getExtra')
            ->willReturn($this->getExtra2());

        return [$package];
    }

    /**
     * @return string[]
     */
    protected function getExtra2()
    {
        return [
            \ConfigOfProject\ConfigTemplateGenerator::NAME_OF_SECTION =>
                [
                    'data_current_project_3',
                    'data_another_project_1',
                    'data_another_project_2',
                ],
        ];
    }

    public function testSaveTemplateCorrect()
    {
        $test_data = ["test"];
        /** @var \PHPUnit_Framework_MockObject_MockObject | \ConfigOfProjectTests\Unit\MockConfigTemplateGeneratorPart2 $generator */
        $generator = $this
            ->getMockBuilder(\ConfigOfProjectTests\Unit\MockConfigTemplateGeneratorPart2::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();
        PHPUnitHelper::callProtectedMethod($generator, 'saveTemplate', [$test_data]);
        $this->assertTrue(file_exists(__DIR__ . "/tmp/template"));
        $this->assertEquals(
            join(PHP_EOL, $test_data),
            file_get_contents(__DIR__ . "/tmp/template"),
            "Содержание файла отличается от ожидаемого"
        );
    }

    public function testSaveTemplateIncorrect()
    {
        $test_data = ["test"];
        /** @var \PHPUnit_Framework_MockObject_MockObject | \ConfigOfProjectTests\Unit\MockConfigTemplateGeneratorPart3 $generator */
        $generator = $this
            ->getMockBuilder(\ConfigOfProjectTests\Unit\MockConfigTemplateGeneratorPart3::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();
        try{
            PHPUnitHelper::callProtectedMethod($generator, 'saveTemplate', [$test_data]);
        }catch (Exception $e){
            $this->assertEquals('Не удалось записать шаблон файла конфигурации: /not_exist_dir/not_file', $e->getMessage());
        }
    }
}




