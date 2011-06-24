<?php
/**
 * Used to annotate properties that should be injected
 *
 * @Target("property")
 *
 * @package BoxUK\Inject\Annotation
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class InjectProperty extends Annotation {

    /**
     * The class to inject for this property - this is optional and will
     * default to the var annotation value.
     * @var string
     */
    public $class;

}
