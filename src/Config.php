<?php

namespace ConfigOfProject;


class Config
{
    const SPACE = ' ';
    const LOCAL_PATH_TO_CONFIG = '/config/';
    const DEFAULT_FILE = 'config.ini';

    protected static $instances = [];

    protected function __construct()
    {

    }

    /**
     * @param string $file
     *
     * @return array
     * @throws Exception
     */
    public static function getConfig($file = self::DEFAULT_FILE)
    {
        $config_absolute_file_path = static::getIniPath($file);

        if (!isset(static::$instances[$config_absolute_file_path])) {

            if (!file_exists($config_absolute_file_path)) {
                throw new \ConfigOfProject\Exception("Не найдено конфигурационного файла '$config_absolute_file_path' для данного проекта");
            }

            $array = parse_ini_file($config_absolute_file_path, true);

            if (is_null($array)) {
                throw new \ConfigOfProject\Exception("Ошибка парсинга конфигурационного файла $config_absolute_file_path");
            }

            static::$instances[$config_absolute_file_path] = static::recursiveParse(static::parseIniAdvanced($array));
        }

        return static::$instances[$config_absolute_file_path];
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected static function getIniPath($file)
    {
        return static::getProjectRoot() . static::LOCAL_PATH_TO_CONFIG . $file;
    }

    /**
     * Своебразные метод заточенный под "количество шагов назад" когда данный проект находится в зависимостях другого
     *
     * @return string
     */
    public static function getProjectRoot()
    {
        return __DIR__ . '/../../../../..';
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected static function recursiveParse($data)
    {
        $result_array = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = self::recursiveParse($value);
                }
                $x = explode('.', $key);
                if (!empty($x[1])) {
                    $x = array_reverse($x, true);
                    if (isset($result_array[$key])) {
                        unset($result_array[$key]);
                    }
                    if (!isset($result_array[$x[0]])) {
                        $result_array[$x[0]] = [];
                    }
                    $first = true;
                    foreach ($x as $k => $v) {
                        if ($first === true) {
                            $b = $data[$key];
                            $first = false;
                        }
                        $b = [$v => $b];
                    }
                    $result_array[$x[0]] = array_merge_recursive($result_array[$x[0]], $b[$x[0]]);
                } else {
                    $result_array[$key] = $data[$key];
                }
            }
        }
        return $result_array;
    }

    protected static function parseIniAdvanced($data)
    {
        $result_array = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $e = explode(':', $key);
                if (!empty($e[1])) {
                    $x = [];
                    foreach ($e as $tk => $tv) {
                        $x[$tk] = trim($tv);
                    }
                    $x = array_reverse($x, true);
                    foreach ($x as $k => $v) {
                        $c = $x[0];
                        if (empty($result_array[$c])) {
                            $result_array[$c] = [];
                        }
                        if (isset($result_array[$x[1]])) {
                            $result_array[$c] = array_merge($result_array[$c], $result_array[$x[1]]);
                        }
                        if ($k === 0) {
                            $result_array[$c] = array_merge($result_array[$c], $data[$key]);
                        }
                    }
                } else {
                    $result_array[$key] = $data[$key];
                }
            }
        }
        return $result_array;
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
