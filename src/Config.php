<?php

namespace ConfigOfProject;

/**
 * Основной файл конфигурации. Точка входа.
 *
 * Class Config
 */
class Config
{
    const SPACE = ' ';
    const LOCAL_PATH_TO_CONFIG = '/config/';
    const DEFAULT_FILE = 'config.ini';

    protected static $instances = [];


    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Config constructor.
     */
    protected function __construct()
    {

    }

    /**
     * Метод реализован НЕ статичным чтобы иметь возможность тестировать
     *
     * @param string $file
     *
     * @return array
     * @throws Exception
     */
    public function getConfig($file = self::DEFAULT_FILE)
    {

        $config_absolute_file_path = $this->getIniPath($file);

        if (!isset(static::$instances[$config_absolute_file_path])) {

            if (!file_exists($config_absolute_file_path)) {
                throw new \ConfigOfProject\Exception("Не найдено конфигурационного файла '$config_absolute_file_path' для данного проекта");
            }

            $array = parse_ini_file($config_absolute_file_path, true);

            static::$instances[$config_absolute_file_path] = $this->parse($array);
        }

        return static::$instances[$config_absolute_file_path];
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getIniPath($file)
    {
        return static::getProjectRoot() . static::LOCAL_PATH_TO_CONFIG . $file;
    }

    /**
     * Своебразные метод заточенный под "количество шагов назад" когда данный проект находится в зависимостях другого
     *
     * @return string
     */
    public function getProjectRoot()
    {
        return __DIR__ . '/../../../../..';
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function parse($data)
    {
        $result_array = [];

        foreach ($data as $key => $value) {
            $tmp_result_array = [];
            $parts_of_key = explode('.', $key);
            $this->setValue($tmp_result_array, $parts_of_key, $value);
            $result_array = array_merge_recursive($result_array, $tmp_result_array);
        }

        return $result_array;
    }

    protected function setValue(array &$result_array, array $parts_of_key, $value)
    {
        $part = array_shift($parts_of_key);
        $result_array[$part] = [];
        if (count($parts_of_key) === 0){
            $result_array[$part] = $value;
            return;
        }
        $this->setValue($result_array[$part], $parts_of_key, $value);
    }

    /**
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $password
     *
     * @return string
     */
    public static function buildConnectionString($host, $dbname, $user = null, $password = null, $port = null)
    {
        assert(is_string($host));
        assert(is_string($dbname));
        assert(is_string($user) || is_null($port));
        assert(is_string($password) || is_null($port));
        assert(is_string($port) || is_null($port));

        $data = [
            'host' => $host,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'port' => $port,
        ];

        $data_for_join = [];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $data_for_join[] = "$key=$value";
            }
        }

        return join(' ', $data_for_join);
    }

    /**
     * @param string $protocol
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $port
     * @param string $dbname
     *
     * @return string
     */
    public static function buildConnectionUrl($protocol, $user, $password, $host, $port, $dbname)
    {
        return "$protocol://$user:$password@$host:$port/$dbname";
    }

}
