<?php

define( 'VERSION', '1.4.3' );

require_once( 'PEAR/PackageFileManager2.php' );
require_once( 'PEAR/PackageFileManager/File.php' );

$packagexml = new PEAR_PackageFileManager2;
$packagexml->setOptions(array(
    'packagedirectory' => 'lib',
    'baseinstalldir' => '/'
));

$packagexml->setPackage( 'inject' );
$packagexml->setSummary( 'Dependency Injection and Reflection' );
$packagexml->setDescription( '-' );
$packagexml->setChannel( 'pear.boxuk.net' );
$packagexml->setAPIVersion( VERSION );
$packagexml->setReleaseVersion( VERSION );
$packagexml->setReleaseStability( 'stable' );
$packagexml->setAPIStability( 'stable' );
$packagexml->setNotes( "-" );
$packagexml->setPackageType( 'php' );
$packagexml->setPhpDep( '5.3.0' );
$packagexml->setPearinstallerDep( '1.3.0' );
$packagexml->addMaintainer( 'lead', 'coders', 'coders', 'coders@boxuk.com' );
$packagexml->setLicense( 'MIT License', 'http://opensource.org/licenses/mit-license.php' );
$packagexml->generateContents();
$packagexml->writePackageFile();
