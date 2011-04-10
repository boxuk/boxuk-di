<?php

require 'BoxUK/Inject/Annotation/InjectMethod.php';
require 'BoxUK/Inject/Annotation/InjectParam.php';
require 'BoxUK/Inject/Annotation/InjectProperty.php';
require 'BoxUK/Inject/Annotation/ScopeSingleton.php';
require 'BoxUK/Inject/Annotation/ScopeSession.php';

function boxuk_autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {
        $file = sprintf(
            '%s/%s.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

boxuk_autoload( __DIR__ );
