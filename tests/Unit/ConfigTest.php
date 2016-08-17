<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 12.08.2016
 * Time: 13:27
 */

namespace ConfigOfProjectTests\Unit;

/**
 * Class ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        \ConfigOfProject\Config::create();
    }

    public function testGetConfigNoFile()
    {
        $file = 'nofile.ini';
        $config_absolute_file_path = __DIR__ . "/" . $file;
        /** @var \ConfigOfProject\Config | \PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this
            ->getMockBuilder(\ConfigOfProject\Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['_', 'getIniPath'])
            ->getMock();

        $config
            ->expects($this->any())
            ->method('getIniPath')
            ->willReturn($config_absolute_file_path);

        try {
            $config->getConfig();
        } catch (\ConfigOfProject\Exception $e) {
            $this->assertEquals("Не найдено конфигурационного файла '$config_absolute_file_path' для данного проекта",
                $e->getMessage()
            );
        }
    }

    public function testGetConfig()
    {
        $file = 'test.ini';
        $config_absolute_file_path = __DIR__ . "/" . $file;
        /** @var \ConfigOfProject\Config | \PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this
            ->getMockBuilder(\ConfigOfProject\Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['_', 'getIniPath', 'parse'])
            ->getMock();

        $config
            ->expects($this->at(0))
            ->method('getIniPath')
            ->willReturn($config_absolute_file_path);

        $config
            ->expects($this->at(1))
            ->method('parse')
            ->willReturn(['test']);


        $result = $config->getConfig();

        $this->assertEquals(['test'], $result);
    }

    public function testGetIniPath()
    {
        $file = 'test.ini';

        /** @var \ConfigOfProject\Config | \PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this
            ->getMockBuilder(\ConfigOfProject\Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();

        $result = \ConfigOfProjectTests\PHPUnitHelper::callProtectedMethod($config, "getIniPath", [$file]);

        $this->assertEquals(1, preg_match('#\/\.\.\/\.\.\/\.\.\/\.\.\/\.\.\/config\/test\.ini#ui', $result));
    }

    public function testBuildConnectionString()
    {
        $host = 'cv.totome.ru';
        $dbname = 'stand';
        $user = 'tigr1991';
        $password = 'qwerty';
        $port = '666';
        $result = \ConfigOfProject\Config::buildConnectionString($host, $dbname, $user, $password, $port);
        $this->assertEquals('host=cv.totome.ru dbname=stand user=tigr1991 password=qwerty port=666', $result);
    }

    public function testBuildConnectionUrl()
    {
        $protocol = 'http';
        $user = 'tigr1991';
        $password = 'qwerty';
        $host = 'cv.totome.ru';
        $port = '666';
        $dbname = 'stand';
        $result = \ConfigOfProject\Config:: buildConnectionUrl($protocol, $user, $password, $host, $port, $dbname);
        $this->assertEquals('http://tigr1991:qwerty@cv.totome.ru:666/stand', $result);
    }

    public function testParse()
    {
        $file = 'test.ini';
        $array = parse_ini_file($file, true);
        $config = $this
            ->getMockBuilder(\ConfigOfProject\Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['_'])
            ->getMock();
        $result = \ConfigOfProjectTests\PHPUnitHelper::callProtectedMethod($config, "parse", [$array]);

        $expected = [
            'aaa' =>
                [
                    'bbb' =>
                        [
                            'ccc' => '1',
                            'ddd' => '2',
                        ],
                    'eee' => '3',
                ],
            'zzz' =>
                [
                    'yyy' => '4',
                ],
        ];

        $this->assertEquals($expected, $result);
    }
}
