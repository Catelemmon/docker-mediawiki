<?php

// @see https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
    exit;
}

if (getenv('MEDIAWIKI_SITENAME') != '') {
    $wgSitename = getenv('MEDIAWIKI_SITENAME');
}

if (getenv('MEDIAWIKI_META_NAMESPACE') != '') {
    $wgMetaNamespace = getenv('MEDIAWIKI_META_NAMESPACE');
}

# Short URLs
$wgScriptPath = "";
$wgArticlePath = "/wiki/$1";
$wgUsePathInfo = true;
$wgScriptExtension = ".php";

if (getenv('MEDIAWIKI_SERVER') == '') {
    throw new Exception('Missing environment variable MEDIAWIKI_SERVER');
} else {
    $wgServer = getenv('MEDIAWIKI_SERVER');
}

$wgResourceBasePath = $wgScriptPath;

$wgLogo = "$wgResourceBasePath/resources/assets/wiki.png";

if (getenv('MEDIAWIKI_EMERGENCY_CONTACT') != '') {
    $wgEmergencyContact = getenv('MEDIAWIKI_EMERGENCY_CONTACT');
}

if (getenv('MEDIAWIKI_PASSWORD_SENDER') != '') {
    $wgPasswordSender = getenv('MEDIAWIKI_PASSWORD_SENDER');
}

$wgDBtype = "mysql";
$wgDBserver = "localhost";
$wgDBprefix = "";

if (getenv('MEDIAWIKI_DB_HOST') != '' || getenv('MEDIAWIKI_DB_PORT') != '') {
    $hostname = ((getenv('MEDIAWIKI_DB_HOST') != '') ? getenv('MEDIAWIKI_DB_HOST') : '127.0.0.1');
    $port = ((getenv('MEDIAWIKI_DB_PORT') != '') ? getenv('MEDIAWIKI_DB_PORT') : '3306');
    $wgDBserver = $hostname.':'.$port;
}

unset($hostname, $port);

if (getenv('MEDIAWIKI_DB_NAME') != '') {
    $wgDBname = getenv('MEDIAWIKI_DB_NAME');
}

if (getenv('MEDIAWIKI_DB_USER') != '') {
    $wgDBuser = getenv('MEDIAWIKI_DB_USER');
}

if (getenv('MEDIAWIKI_DB_PASSWORD') != '') {
    $wgDBpassword = getenv('MEDIAWIKI_DB_PASSWORD');
}

$wgEnableUploads = true;

$wgUploadPath = '/images';
$wgUploadDirectory = '/images';

$wgUploadSizeWarning = false;

$wgEnableUploads = true;

if (getenv('MEDIAWIKI_MAX_UPLOAD_SIZE') != '') {
    // Since MediaWiki's config takes upload size in bytes and PHP in 100M format, lets use PHPs format and convert that here.
    $maxUploadSize = getenv('MEDIAWIKI_MAX_UPLOAD_SIZE');
    if (strlen($maxUploadSize) >= 2) {
        $maxUploadSizeUnit = substr($maxUploadSize, -1, 1);
        $maxUploadSizeValue = (integer)substr($maxUploadSize, 0, -1);
        switch (strtoupper($maxUploadSizeUnit)) {
            case 'G':
                $maxUploadSizeFactor = 1024 * 1024 * 1024;
                break;
            case 'M':
                $maxUploadSizeFactor = 1024 * 1024;
                break;
            case 'K':
                $maxUploadSizeFactor = 1024;
                break;
            case 'B':
            default:
                $maxUploadSizeFactor = 0;
                break;
        }
        $wgMaxUploadSize = $maxUploadSizeValue * $maxUploadSizeFactor;
        unset($maxUploadSizeUnit, $maxUploadSizeValue, $maxUploadSizeFactor);
    }
}

if (getenv('MEDIAWIKI_FILE_EXTENSIONS') != '') {
    foreach (explode(',', getenv('MEDIAWIKI_FILE_EXTENSIONS')) as $extension) {
        $wgFileExtensions[] = trim($extension);
    }
}

$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgShellLocale = "C.UTF-8";

if (getenv('MEDIAWIKI_LANGUAGE_CODE') != '') {
    $wgLanguageCode = getenv('MEDIAWIKI_LANGUAGE_CODE');
}

if (getenv('MEDIAWIKI_SECRET_KEY') != '') {
    $wgSecretKey = getenv('MEDIAWIKI_SECRET_KEY');
}

if (getenv('MEDIAWIKI_UPGRADE_KEY') != '') {
    $wgUpgradeKey = getenv('MEDIAWIKI_UPGRADE_KEY');
}

$wgRightsPage = "";
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

$wgDiff3 = "/usr/bin/diff3";


$wgGroupPermissions['*']['createaccount'] = false;
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['read'] = false;

$wgDefaultSkin = "vector";
if (getenv('MEDIAWIKI_DEFAULT_SKIN') != '') {
    $wgDefaultSkin = getenv('MEDIAWIKI_DEFAULT_SKIN');
}

# Enabled skins
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Timeless' );
wfLoadSkin( 'Vector' );

# Debug
if (getenv('MEDIAWIKI_DEBUG') == '1') {
    $wgShowExceptionDetails = true;
    $wgShowSQLErrors = true;
    $wgDebugDumpSql = true;
    $wgDebugLogFile = "/tmp/wiki-debug.log";
}

wfLoadExtension( 'CategoryTree' );
wfLoadExtension( 'Cite' );
wfLoadExtension( 'CiteThisPage' );
wfLoadExtension( 'CodeEditor' );
wfLoadExtension( 'ConfirmEdit' );
wfLoadExtension( 'ImageMap' );
wfLoadExtension( 'InputBox' );
wfLoadExtension( 'Interwiki' );
wfLoadExtension( 'LocalisationUpdate' );
wfLoadExtension( 'MultimediaViewer' );
wfLoadExtension( 'Nuke' );
wfLoadExtension( 'OATHAuth' );
wfLoadExtension( 'PageImages' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'PdfHandler' );
wfLoadExtension( 'Poem' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'ReplaceText' );
wfLoadExtension( 'Scribunto' );
wfLoadExtension( 'SpamBlacklist' );
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'TextExtracts' );
wfLoadExtension( 'WikiEditor' );


# VisualEditor
wfLoadExtension( 'VisualEditor' );
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgHiddenPrefs[] = 'visualeditor-enable';
$wgVisualEditorAllowLossySwitching=false;

# parsoid
$wgVirtualRestConfig['modules']['parsoid'] = array(
    // URL to the Parsoid instance
    // Use port 8142 if you use the Debian package
    'url' => 'http://localhost:8142',
    // Parsoid "domain", see below (optional)
    'domain' => 'localhost',
    // Parsoid "prefix", see below (optional)
    'prefix' => 'localhost'
);


# User Merge
wfLoadExtension('UserMerge');
$wgGroupPermissions['bureaucrat']['usermerge'] = true;
$wgGroupPermissions['sysop']['usermerge'] = true;
$wgUserMergeProtectedGroups = array();

# math extension
wfLoadExtension( 'Math' );
$wgDebugLogGroups['Math'] = [ 'level' => 'info', 'destination' => '/var/www/html/mediawiki/log/math.log' ];
$wgMathValidModes[] = 'mathml';
$wgDefaultUserOptions['math'] = 'mathml';
$wgMathoidCli = ['/usr/lib/mathoid/cli.js', '-c', '/usr/lib/mathoid/config.yaml'];
$wgMathMathMLUrl = 'http://localhost:10044/';
$wgMaxShellMemory = 1228800;


# Load extra settings
require 'ExtraLocalSettings.php';
