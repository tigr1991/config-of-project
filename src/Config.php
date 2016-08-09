<?php
/**
 * Created by PhpStorm.
 * User: vasily
 * Date: 02.10.15
 * Time: 15:49
 */

namespace MivarUtils\Common;


class Config
{
    const SPACE = ' ';

    protected static $instances = [];

    protected function __construct()
    {

    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getConfig()
    {

        $configAbsoluteFilePath = static::getIniPath();

        if (!isset(self::$instances[$configAbsoluteFilePath])) {

            if (!file_exists($configAbsoluteFilePath)) {
                throw new \LogicException("There is no configuration file $configAbsoluteFilePath for this project");
            }

            $array = parse_ini_file($configAbsoluteFilePath, true);

            if (is_null($array)) {
                throw new \LogicException("Parse error of a configuration file $configAbsoluteFilePath");
            }


            self::$instances[$configAbsoluteFilePath] = self::recursive_parse(self::parse_ini_advanced($array));
        }

        return self::$instances[$configAbsoluteFilePath];
    }

    protected static function getIniPath()
    {

        return self::getProjectRoot() . '/config/configuration.ini';
    }

    public static function getProjectRoot()
    {
        return __DIR__ . '/../../../../..';
    }

    protected static function recursive_parse($array)
    {
        $returnArray = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = self::recursive_parse($value);
                }
                $x = explode('.', $key);
                if (!empty($x[1])) {
                    $x = array_reverse($x, true);
                    if (isset($returnArray[$key])) {
                        unset($returnArray[$key]);
                    }
                    if (!isset($returnArray[$x[0]])) {
                        $returnArray[$x[0]] = array();
                    }
                    $first = true;
                    foreach ($x as $k => $v) {
                        if ($first === true) {
                            $b = $array[$key];
                            $first = false;
                        }
                        $b = array($v => $b);
                    }
                    $returnArray[$x[0]] = array_merge_recursive($returnArray[$x[0]], $b[$x[0]]);
                } else {
                    $returnArray[$key] = $array[$key];
                }
            }
        }
        return $returnArray;
    }

    protected static function parse_ini_advanced($array)
    {
        $returnArray = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $e = explode(':', $key);
                if (!empty($e[1])) {
                    $x = array();
                    foreach ($e as $tk => $tv) {
                        $x[$tk] = trim($tv);
                    }
                    $x = array_reverse($x, true);
                    foreach ($x as $k => $v) {
                        $c = $x[0];
                        if (empty($returnArray[$c])) {
                            $returnArray[$c] = array();
                        }
                        if (isset($returnArray[$x[1]])) {
                            $returnArray[$c] = array_merge($returnArray[$c], $returnArray[$x[1]]);
                        }
                        if ($k === 0) {
                            $returnArray[$c] = array_merge($returnArray[$c], $array[$key]);
                        }
                    }
                } else {
                    $returnArray[$key] = $array[$key];
                }
            }
        }
        return $returnArray;
    }

    /**
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $password
     * @return string
     */
    public static function build_connection_string($host, $dbname, $user = null, $password = null, $port = null)
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
     * @return string
     */
    public static function build_connection_url($protocol, $user, $password, $host, $port, $dbname)
    {
        return "$protocol://$user:$password@$host:$port/$dbname";
    }

    /**
     * @return string
     */
    public static function getDefaultCacheDir()
    {
        $dir = static::getProjectRoot() . '/cache';

        if (!is_dir($dir)) {
            throw new \LogicException("must be directory " . var_export($dir, 1));
        }

        return $dir;
    }
}
