<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 12.08.2016
 * Time: 18:24
 */

namespace ConfigOfProjectTests\Unit;

/**
 * Class ExceptionTest
 * @package ConfigOfProjectTests\Unit
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testThrow()
    {
        try {
            throw new \ConfigOfProject\Exception('test');
        } catch (\ConfigOfProject\Exception $e) {
            $this->assertEquals('test', $e->getMessage());
        }
    }
}
