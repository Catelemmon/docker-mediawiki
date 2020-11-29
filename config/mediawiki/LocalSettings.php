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
                $maxUploadSizeFactor = 1;
                break;
        }
        $wgMaxUploadSize = $maxUploadSizeValue * $maxUploadSizeFactor;
        unset($maxUploadSizeUnit, $maxUploadSizeValue, $maxUploadSizeFactor);
    }
}

$wgMaxUploadSize = 2147483648;

if (getenv('MEDIAWIKI_FILE_EXTENSIONS') != '') {
    foreach (explode(',', getenv('MEDIAWIKI_FILE_EXTENSIONS')) as $extension) {
        $externalExts[] = trim($extension);
    }
} else{
    $externalExts = [];
}
$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg' );
$wgFileExtensions = array_merge($wgFileExtensions, ["xlsx", "xls", "doc", "docx", "mp4", "mkv", "avi","pdf"], $externalExts);

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
$wgGroupPermissions['*']['edit'] = true;
$wgGroupPermissions['*']['read'] = true;

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
$wgVirtualRestConfig['modules']['parsoid']['forwardCookies'] = true;
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

# pending changes 审核功能 仅开放已经审阅的内容给用户
$wgFlaggedRevsStatsAge = false;
require_once("/var/www/mediawiki/extensions/FlaggedRevs/FlaggedRevs.php");

# math extension
wfLoadExtension('Math');
$wgDebugLogGroups['Math'] = [ 'level' => 'info', 'destination' => '/var/www/html/mediawiki/log/math.log' ];
$wgMathValidModes[] = 'mathml';
$wgDefaultUserOptions['math'] = 'mathml';
$wgMathoidCli = ['/usr/lib/mathoid/cli.js', '-c', '/usr/lib/mathoid/config.yaml'];
$wgMathMathMLUrl = 'http://localhost:10044/';
$wgMaxShellMemory = 1228800;

# Widghts
wfLoadExtension('Widgets');

# ContributionScores
require_once("/var/www/mediawiki/extensions/ContributionScores/ContributionScores.php");
$wgContribScoreIgnoreBots = true;          // Exclude Bots from the reporting - Can be omitted.
$wgContribScoreIgnoreBlockedUsers = true;  // Exclude Blocked Users from the reporting - Can be omitted.
$wgContribScoresUseRealName = true;        // Use real user names when available - Can be omitted. Only for MediaWiki 1.19 and later.
$wgContribScoreDisableCache = false;       // Set to true to disable cache for parser function and inclusion of table.
$wgContribScoreReports = array(
                      array(7,50),
                      array(30,50),
                      array(0,50),
                      array(90,50),
                      array(365,50));


wfLoadExtension('PDFEmbed');
$wgPdfEmbed['width'] = 800;
$wgPdfEmbed['height'] = 1090;
$wgGroupPermissions['*']['embed_pdf'] = true;

# Load extra settings
require 'ExtraLocalSettings.php';
