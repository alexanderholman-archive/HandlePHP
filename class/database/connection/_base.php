<?php
namespace handle\php\database;
use handle;
/**
 * Class protocol
 * @property handle\php $base
 * @property handle\php\database $family
 * @property handle\php\database $parent
 * @property connection $current
 * @method handle\php base
 * @method handle\php\database family
 * @method handle\php\database parent
 * @method connection current
 */
class connection extends handle\php\database {
    use handle\framework;
}