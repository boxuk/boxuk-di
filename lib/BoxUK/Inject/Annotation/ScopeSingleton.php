<?php
/**
 * Used to annotate a class as having singleton scope
 *
 * @Target("class")
 * 
 * @package BoxUK\Inject\Annotation
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class ScopeSingleton extends Annotation {

    /**
     * @var string Defines the interface that this class implements
     */
    public $implements;

}
