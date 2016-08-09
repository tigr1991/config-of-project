<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 27.11.2015
 * Time: 13:41
 */

namespace MivarUtils\Common;

class ConfigTemplateGenerator
{
    const NAME_OF_SECTION = 'mivar-configuration';

    protected static function getPath()
    {
        return __DIR__ . '/../../../../../config/configuration.ini.template';
    }

    /**
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public static function generate(\Composer\Script\Event $event)
    {
        $composer = $event->getComposer();
        /** @var \Composer\Package\BasePackage $package */
        $package = $composer->getPackage();
        $extra = $package->getExtra();

        $res = [];
        if (!empty($extra)) {
            if (array_key_exists(static::NAME_OF_SECTION, $extra)) {
                $res = $extra[static::NAME_OF_SECTION];
            }
        }

        /** @var \Composer\Repository\RepositoryManager $repository_manager */
        $repository_manager = $composer->getRepositoryManager();
        /** @var \Composer\Repository\ArrayRepository $repository */
        foreach ($repository_manager->getRepositories() as $repository) {

            if ($repository->count() === 0) {
                continue;
            }
            foreach ($repository->getPackages() as $package) {
                $extra = $package->getExtra();
                if (!empty($extra[static::NAME_OF_SECTION])) {
                    $res = array_merge($res, $extra[static::NAME_OF_SECTION]);
                }
            }
        }

        $result_tmp = array_unique($res);

        //Добавляем пустые строки между блоками
        $result = [];
        $prev_first_section = null;
        foreach ($result_tmp as $item) {
            list($first_section) = preg_split('/\./', $item);
            if ($prev_first_section === null) {
                $prev_first_section = $first_section;
                $result[] = $item;
                continue;
            }
            if ($prev_first_section !== $first_section) {
                $result[] = '';
            }
            $result[] = $item;
            $prev_first_section = $first_section;
        }

        $configAbsoluteFilePath = \MivarUtils\Common\ConfigTemplateGenerator::getPath();
        if (file_put_contents($configAbsoluteFilePath, join(PHP_EOL, $result)) === false) {
            throw new \LogicException("Не удалось записать шаблон файла конфигурации: ".$configAbsoluteFilePath);
        }
    }
}