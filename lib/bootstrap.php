<?php

require 'BoxUK/Inject/Annotation/InjectMethod.class.php';
require 'BoxUK/Inject/Annotation/InjectParam.class.php';
require 'BoxUK/Inject/Annotation/ScopeSingleton.class.php';
require 'BoxUK/Inject/Annotation/ScopeSession.class.php';

function boxuk_autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {
        $file = sprintf(
            '%s/%s.class.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

boxuk_autoload( __DIR__ );
