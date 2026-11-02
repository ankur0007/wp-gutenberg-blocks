<?php
namespace AVCL\Blocks;
class Init{
    private static $instances = [];

    public static function init(...$args) {
        $class = static::class;

        if ( ! isset( self::$instances[ $class ] ) ) {
            self::$instances[ $class ] = new static(...$args);
        }

        return self::$instances[ $class ];
    }
}
?>