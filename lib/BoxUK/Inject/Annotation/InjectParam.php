<?php
/**
 * Used to annotate parameter types to inject into methods
 *
 * @Target("method")
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class InjectParam extends Annotation {

    /**
     * @var string The name of the variable we want injected
     */
    public $variable;

    /**
     * @var string The class name of the variable to inject
     */
    public $class;

}
