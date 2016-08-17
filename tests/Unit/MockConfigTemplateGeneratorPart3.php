<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 16.08.2016
 * Time: 20:08
 */

namespace ConfigOfProjectTests\Unit;


class MockConfigTemplateGeneratorPart3 extends \ConfigOfProject\ConfigTemplateGenerator
{
    /**
     * @return string
     */
    protected static function getPath()
    {
        return '/not_exist_dir/not_file';
    }
}