<?php
# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
  exit;
}

/* DEBUG ONLY */
#$wgShowExceptionDetails = true;

#General Settings
$wgSitename = "Star Citizen Wiki";
$wgMetaNamespace = "Star_Citizen";
$wgAllowSiteCSSOnRestrictedPages = true;

#Controls if the main page should be served as the domain root.
$wgMainPageIsDomainRoot = true;

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";
$wgScriptExtension = "$wgScriptPath/index.php";
$wgRedirectScript   = "$wgScriptPath/redirect.php";
$wgArticlePath = "/$1";

$wgShowSQLErrors = false;
$wgDebugDumpSql = false;
$wgDebugComments = false;

## The protocol and server name to use in fully-qualified URLs
#$wgServer = ""; NOW PLACED IN EXTERNAL INCLUDES FOLDER

## Force HTTPS
$wgForceHTTPS = true;

## Content Security Policy
## hCaptcha is required for VE
## Flickr API is required for UploadWizard
$wgCSPHeader = [
	'useNonces' => true,
	'unsafeFallback' => false,
	'script-src' => [ 
		'\'self\'',
		'https://www.google-analytics.com',
		'https://hcaptcha.com',
		'https://*.hcaptcha.com'
	],
	'default-src' => [ 
		'\'self\'',
		'https://api.flickr.com',
		'https://www.google-analytics.com',
		'https://hcaptcha.com', 
		'https://*.hcaptcha.com',
	],
	'style-src' => [ '\'self\'',  ],
	'object-src' => [ '\'none\'' ],
];

## Cookies policy
## Strict - Cookies for me and not for thee
$wgCookieSameSite = 'Strict';
## Only send over HTTPS
$wgCookieSecure = true;

## Referrer policy
$wgReferrerPolicy = array('strict-origin-when-cross-origin', 'strict-origin');

## Output a canonical meta tag on every page
$wgEnableCanonicalServerLink = true;

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogos = [
	'svg' => "$wgResourceBasePath/resources/assets/sitelogo.svg",
];

$wgFavicon = "$wgResourceBasePath/resources/assets/favicon.ico";
$wgAppleTouchIcon = "$wgResourceBasePath/resources/assets/apple-touch-icon.png";

## UPO means: this is also a user preference option
$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "webmaster@starcitizen.tools";
$wgPasswordSender = "do-not-reply@starcitizen.tools";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Allow logged-in users to set a preference whether or not matches 
## in search results should force redirection to that page.
$wgSearchMatchRedirectPreference = true;

# Disable the real name field
$wgHiddenPrefs[] = 'realname';

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=utf8";

## Shared memory settings
$wgMainCacheType = 'redis';
$wgSessionCacheType = 'redis';
$wgMemCachedServers = array();

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgGenerateThumbnailOnParse = false;
#$wgThumbnailScriptPath = "{$wgScriptPath}/thumb.php";
$wgUseImageMagick = true;
$wgThumbnailEpoch = "20190815000000";
$wgIgnoreImageErrors = true;

$wgMaxImageArea = 6.4e7;

# Gallery settings
$wgGalleryOptions = [
  'mode' => 'packed-overlay', // One of "traditional", "nolines", "packed", "packed-hover", "packed-overlay", "slideshow" (1.28+)
];

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = true;

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
#$wgHashedUploadDirectory = false;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
$wgCacheDirectory = "$IP/cache";

# Expiry time for the footer link cache, in seconds, or 0 if disabled
# 31536000 - 1 year
$wgFooterLinkCacheExpiry = 31536000;

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/4.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";

# The following permissions were set based on your choice in the installer
$wgAllowUserCss = true;

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = 'citizen';

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'Citizen' );

# Citizen skin config
# Enable Preconnect for the defined domain
$wgCitizenEnablePreconnect = true;
$wgCitizenPreconnectURL = 'https://www.google-analytics.com';
# HSTS
$wgCitizenEnableHSTS = true;
$wgCitizenHSTSMaxAge = 63072000; # 2 year
$wgCitizenHSTSIncludeSubdomains = true;
$wgCitizenHSTSPreload = true;
# Enable the deny X-Frame-Options header
$wgCitizenEnableDenyXFrameOptions = true;
# Enable X-XSS-Protection header
$wgCitizenEnableXXSSProtection = true;
# Enable strict-origin-when-cross-origin referrer policy	
$wgCitizenEnableStrictReferrerPolicy = true;
# Feature policy
$wgCitizenEnableFeaturePolicy = true;
$wgCitizenFeaturePolicyDirective = 'accelerometer \'none\'; autoplay \'none\'; camera \'none\'; geolocation \'none\'; gyroscope \'none\'; magnetometer \'none\'; microphone \'none\'; payment \'none\'; usb \'none\'' ;
# Permissions policy
$wgCitizenEnablePermissionsPolicy = true;
$wgCitizenPermissionsPolicyDirective = 'accelerometer=(), autoplay=(), camera=(), geolocation=(), gyroscope=(), interest-cohort=(), magnetometer=(), microphone=(), payment=(), usb=()' ;
# Use REST API search endpoint
$wgCitizenSearchGateway = 'mwRestApi';
# Search description source
$wgCitizenSearchDescriptionSource = 'wikidata';
# Number of search results in suggestion
$wgCitizenMaxSearchResults = 10;

#Maintenance
#$wgReadOnly = 'Maintenance is underway. Website is on read-only mode';

#SVG Support
$wgFileExtensions[] = 'svg';
$wgAllowTitlesInSVG = true;
$wgSVGConverter = 'ImageMagick';

#Open external link in new tab/window
$wgExternalLinkTarget = '_blank';

#Enable native lazyloading
$wgNativeImageLazyLoading = true;

#Non-dynamic footer links cache
#604800 - 1 week
$wgFooterLinkCacheExpiry = 604800;

#=============================================== External Includes ===============================================

require_once("/home/www-data/external_includes/mysql_pw.php");
require_once("/home/www-data/external_includes/secret_keys.php");

#=============================================== Extension Load ===============================================

#wfLoadExtension( 'CleanChanges' );
wfLoadExtension( 'AdvancedSearch' );
wfLoadExtension( 'Babel' );
wfLoadExtension( 'CategoryTree' );
wfLoadExtension( 'CheckUser' );
wfLoadExtension( 'CirrusSearch' );
wfLoadExtension( 'Cite' );
wfLoadExtension( 'CiteThisPage' );
wfLoadExtension( 'cldr' );
#wfLoadExtension( 'CloudFlare' );
wfLoadExtension( 'CodeEditor' );
wfLoadExtension( 'CodeMirror' );
wfLoadExtension( 'CommonsMetadata' );
wfLoadExtensions([ 'ConfirmEdit', 'ConfirmEdit/hCaptcha' ]);
wfLoadExtension( 'CookieWarning' );
wfLoadExtension( 'Disambiguator' );
wfLoadExtension( 'Discord' );
wfLoadExtension( 'DismissableSiteNotice' );
wfLoadExtension( 'DynamicPageList' );
wfLoadExtension( 'Echo' );
wfLoadExtension( 'Elastica' );
wfLoadExtension( 'EmbedVideo' );
wfLoadExtension( 'EventLogging' );
wfLoadExtension( 'EventStreamConfig' );
wfLoadExtension( 'ExternalData' );
wfLoadExtension( 'Flow' );
wfLoadExtension( 'GoogleAnalytics' );
wfLoadExtension( 'Graph' );
wfLoadExtension( 'InputBox' );
wfLoadExtension( 'JsonConfig' );
wfLoadExtension( 'LocalisationUpdate' );
wfLoadExtension( 'Loops' );
wfLoadExtension( 'MultimediaViewer' );
wfLoadExtension( 'NativeSvgHandler' );
wfLoadExtension( 'Nuke' );
wfLoadExtension( 'OATHAuth' );
wfLoadExtension( 'PageImages' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'Popups' );
wfLoadExtension( 'Purge' );
wfLoadExtension( 'RelatedArticles' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'ReplaceText' );
wfLoadExtension( 'RevisionSlider' );
wfLoadExtension( 'RSS' );
wfLoadExtension( 'SandboxLink' );
wfLoadExtension( 'Scribunto' );
wfLoadExtension( 'ShortDescription' );
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'TabberNeue' );
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'TemplateStyles' );
wfLoadExtension( 'TemplateStylesExtender' );
wfLoadExtension( 'TextExtracts' );
wfLoadExtension( 'Thanks' );
wfLoadExtension( 'UniversalLanguageSelector' );
wfLoadExtension( 'UploadWizard' );
wfLoadExtension( 'Variables' );
wfLoadExtension( 'VisualEditor' );
wfLoadExtension( 'WikiEditor' );
wfLoadExtension( 'WikiSEO' );

#=============================================== Extension Config ===============================================
# PageImages
$wgPageImagesNamespaces = array( 'NS_MAIN','NS_UPDATE', 'NS_GUIDE', 'NS_COMMLINK', 'NS_ORG' )
$wgPageImagesOpenGraphFallbackImage = "$wgResourceBasePath/resources/assets/sitelogo.svg" 
	
#CirrusSearch
$wgSearchType = 'CirrusSearch';
$wgCirrusSearchUseCompletionSuggester = 'yes';
$wgCirrusSearchCompletionSuggesterSubphrases = [
    'build'  => true,
    'use' => true,
    'type' => 'anywords',
    'limit' => 5,
];

#DismissableSiteNotice
$wgDismissableSiteNoticeForAnons = true;

#Flow
$wgFlowEditorList = array( 'visualeditor', 'none' );
$wgFlowContentFormat = 'html';

#UploadWizard
$wgApiFrameOptions = 'SAMEORIGIN';
$wgAllowCopyUploads = true;
$wgCopyUploadsDomains = array( '*.flickr.com', '*.staticflickr.com' );
$wgUploadNavigationUrl = '/Special:UploadWizard';
$wgUploadWizardConfig = array(
  'debug' => false,
  'altUploadForm' => 'Special:Upload',
  'fallbackToAltUploadForm' => false,
  'alternativeUploadToolsPage' => false,
  'enableFormData' => true,
  'enableMultipleFiles' => true,
  'enableMultiFileSelect' => false,
  'tutorial' => array(
    'skip' => true
  ),
  'maxUploads' => 15,
  'fileExtensions' => $wgFileExtensions,
  'flickrApiUrl' => 'https://api.flickr.com/services/rest/?',
  'licenses' => array(
    # Cloud Imperium license
    'rsilicense' => array(
      'msg' => 'mwe-upwiz-license-rsi',
      'templates' => array('RSIlicense')
    ),
    # CC-BY-NC-SA-2.0 required by Flickr
    # Note that this need to be added to mw.FlickrChecker.js every time it is updated
    'cc-by-nc-sa-2.0' => array(
      'msg' => 'mwe-upwiz-license-cc-by-nc-sa-2.0',
      'templates' => array('cc-by-nc-sa-2.0'),
      #'icons' => array('cc-by','cc-nc','cc-sa'), NC icon is missing
      'url' => '//creativecommons.org/licenses/by-nc-sa/2.0/',
      'languageCodePrefix' => 'deed.'
    ),
    # CC-BY-NC-2.0 required by Flickr
    # Note that this need to be added to mw.FlickrChecker.js every time it is updated
    'cc-by-nc-2.0' => array(
      'msg' => 'mwe-upwiz-license-cc-by-nc-2.0',
      'templates' => array('cc-by-nc-2.0'),
      #'icons' => array('cc-by','cc-nc'), NC icon is missing
      'url' => '//creativecommons.org/licenses/by-nc/2.0/',
      'languageCodePrefix' => 'deed.'
    ),
  ),
  # License selection page
  'licensing' => array(
    'thirdParty' => array(
      'type' => 'or',
      'defaults' => 'rsilicense',
      'licenseGroups' => array(
        array(
          'head' => 'mwe-upwiz-license-sc-head',
          'licenses' => array(
            'rsilicense'
          )
        ),
        array(
          # This should be a list of all CC licenses we can reasonably expect to find around the web
          'head' => 'mwe-upwiz-license-cc-head',
          'subhead' => 'mwe-upwiz-license-cc-subhead',
          'licenses' => array(
            'cc-by-sa-4.0',
            'cc-by-sa-3.0',
            'cc-by-sa-2.5',
            'cc-by-4.0',
            'cc-by-3.0',
            'cc-by-2.5',
            'cc-zero'
          )
        ),
        array(
          # Flickr still uses CC 2.0
          'head' => 'mwe-upwiz-license-flickr-head',
          'subhead'=> 'mwe-upwiz-license-flickr-subhead',
          'licenses'=> array(
            'cc-by-nc-sa-2.0',
            'cc-by-nc-2.0',
            'cc-by-sa-2.0',
            'cc-by-2.0'
          )
        ),
        array(
          'head' => 'mwe-upwiz-license-custom-head',
          'special' => 'custom',
          'licenses' => array( 'custom' ),
        ),
        array(
          'head' => 'mwe-upwiz-license-none-head',
          'licenses' => array( 'none' )
        ),
      )
    )
  )
);

#TextExtracts
$wgExtractsRemoveClasses[] = 'dd';
$wgExtractsRemoveClasses[] = 'dablink';
$wgExtractsRemoveClasses[] = 'translate';

#WikiSEO
#Disable wgLogo as fallback image
$wgWikiSeoDisableLogoFallbackImage = true;
#TextExtracts description for SEO
$wgWikiSeoEnableAutoDescription = true;
$wgWikiSeoTryCleanAutoDescription = true;

#MultimediaViewer
$wgMediaViewerEnableByDefault = true;
$wgMediaViewerEnableByDefaultForAnonymous = true;

#ConfirmEdit
#$wgCaptchaClass = 'ReCaptchaNoCaptcha';
$wgCaptchaTriggers['edit']          = true;
$wgCaptchaTriggers['create']        = true;

#CleanChanges
#$wgCCTrailerFilter = true;
#$wgCCUserFilter = false;
#$wgDefaultUserOptions['usenewrc'] = 1;

#Translate
$wgLocalisationUpdateDirectory = "$IP/cache";
$wgTranslateDocumentationLanguageCode = 'qqq';
$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages

$wgTranslateBlacklist = array(
    '*' => array( // All groups
      'en' => 'English is the source language.',
      'zh-cn' => 'This langauge is disabled.',
      'zh-sg' => 'This langauge is disabled.',
      'zh-hk' => 'This langauge is disabled.',
      'zh-mo' => 'This langauge is disabled.',
      'zh-tw' => 'This langauge is disabled.',
      'zh-yue' => 'This langauge is disabled.',
      'zh-my' => 'This langauge is disabled.',
      'zh' => 'This langauge is disabled.',
    ),
);

#Universal Language Selector
# Disable GeoService
$wgULSGeoService = false;
# Disable language detection as some message fallback are broken
# Copyright notice and footer does not appear
$wgULSLanguageDetection = false;
# Disable IME
$wgULSIMEEnabled = false;

#Google Analytics
$wgGoogleAnalyticsAccount = 'UA-48789297-5';
# No bot group analytics.
$wgGroupPermissions['bot']['noanalytics'] = true;

#ExternalData
# $edgCacheTable = 'ed_url_cache'; Need to run ExternalData.sql first
# $wgHTTPTimeout = 60; Set HTTP request timeout to 60s
$edgCacheExpireTime = 3 * 24 * 60 * 60;
$edgAllowExternalDataFrom = array( 'https://starcitizen.tools' );
$edgExternalValueVerbose = false;

#Visual Editor
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";
$wgDefaultUserOptions['visualeditor-newwikitext'] = 1;
$wgPrefs[] = 'visualeditor-enable';
$wgVisualEditorEnableWikitext = true;
$wgVisualEditorEnableDiffPage = true;
$wgVisualEditorUseSingleEditTab = true;
$wgVisualEditorEnableVisualSectionEditing = true;

#Code Editor
$wgDefaultUserOptions['usebetatoolbar'] = 1; // user option provided by WikiEditor extension

#Popups
#Reference Previews are enabled for all users by default
$wgPopupsReferencePreviewsBetaFeature = false;

#RelatedArticles 
$wgRelatedArticlesFooterWhitelistedSkins = [ 'citizen' ];
$wgRelatedArticlesDescriptionSource = 'wikidata';
$wgRelatedArticlesUseCirrusSearch = true;
$wgRelatedArticlesOnlyUseCirrusSearch = true;

#Eventlogging
$wgEventLoggingBaseUri = 'https://starcitizen.tools:8080/event.gif';
$wgEventLoggingFile = '/var/log/mediawiki/events.log';

#Scribunto
$wgScribuntoDefaultEngine = 'luasandbox';

#Echo
$wgAllowHTMLEmail = true;

#Redis
/** @see RedisBagOStuff for a full explanation of these options. **/

$wgObjectCaches['redis'] = array(
    'class'                => 'RedisBagOStuff',
    'servers'              => array( '127.0.0.1:6379' ),
    // 'connectTimeout'    => 1,
    // 'persistent'        => false,
    // 'password'          => 'secret',
    // 'automaticFailOver' => true,
);

$wgJobTypeConf['default'] = [
	'class' => 'JobQueueRedis',
	'order' => 'fifo',
	'redisServer' => '127.0.0.1:6379',
	'checkDelay' => true,
	'daemonized' => true
];

$wgJobQueueAggregator = [
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => '127.0.0.1:6379',
];

#$wgJobTypeConf['default'] = array(
#  'class'          => 'JobQueueRedis',
#  'redisServer'    => '127.0.0.1:6379',

#  'claimTTL'       => 3600
#);

#CookieWarning
$wgCookieWarningEnabled = true;
$wgCookieWarningGeoIPLookup = 'none';

#DynamicPageList
$wgDplSettings['recursiveTagParse'] = true;
$wgDplSettings['allowUnlimitedResults'] = true;
#TemplateStyles
$wgTemplateStylesAllowedUrls = [
  "audio" => [
    "<^https://starcitizen\\.tools/>",
    "<^https://scwdev\\.czen\\.me/>"
  ],
  "image" => [
    "<^https://starcitizen\\.tools/>",
    "<^https://scwdev\\.czen\\.me/>"
  ],
  "svg" => [
    "<^https://starcitizen\\.tools/[^?#]*\\.svg(?:[?#]|$)>",
    "<^https://scwdev\\.czen\\.me/[^?#]*\\.svg(?:[?#]|$)>"
  ],
  "font" => [
    "<^https://starcitizen\\.tools/>",
    "<^https://scwdev\\.czen\\.me/>"
  ],
  "namespace" => [
      "<.>"
  ],
  "css" => []
];

#=============================================== Namespaces ===============================================
define("NS_COMMLINK", 3000);
define("NS_COMMLINK_TALK", 3001);
$wgExtraNamespaces[NS_COMMLINK] = "Comm-Link";
$wgExtraNamespaces[NS_COMMLINK_TALK] = "Comm-Link_talk";
$wgNamespacesWithSubpages[NS_COMMLINK] = true;
$wgNamespacesToBeSearchedDefault[NS_COMMLINK] = true;

define("NS_PROJMGMT", 3002);
define("NS_PROJMGMT_TALK", 3003);
$wgExtraNamespaces[NS_PROJMGMT] = "ProjMGMT";
$wgExtraNamespaces[NS_PROJMGMT_TALK] = "ProjMGMT_talk";
$wgNamespacesWithSubpages[NS_PROJMGMT] = true;

define("NS_ISSUE", 3004);
define("NS_ISSUE_TALK", 3005);
$wgExtraNamespaces[NS_ISSUE] = "Issue";
$wgExtraNamespaces[NS_ISSUE_TALK] = "Issue_talk";
$wgNamespacesWithSubpages[NS_ISSUE] = true;

define("NS_GUIDE", 3006);
define("NS_GUIDE_TALK", 3007);
$wgExtraNamespaces[NS_GUIDE] = "Guide";
$wgExtraNamespaces[NS_GUIDE_TALK] = "Guide_talk";
$wgNamespacesWithSubpages[NS_GUIDE] = true;
$wgNamespacesToBeSearchedDefault[NS_GUIDE] = true;

define("NS_ORG", 3008);
define("NS_ORG_TALK", 3009);
$wgExtraNamespaces[NS_ORG] = "ORG";
$wgExtraNamespaces[NS_ORG_TALK] = "ORG_talk";
$wgNamespacesWithSubpages[NS_ORG] = true;

# Deleted NS 3010 - 3015 skipped ID to avoid issues

define("NS_UPDATE", 3016);
define("NS_UPDATE_TALK", 3017);
$wgExtraNamespaces[NS_UPDATE] = "Update";
$wgExtraNamespaces[NS_UPDATE_TALK] = "Update_talk";
$wgNamespacesWithSubpages[NS_UPDATE] = true;

$wgNamespaceProtection[NS_TEMPLATE] = array( 'template-edit' );
$wgNamespaceProtection[NS_COMMLINK] = array( 'commlink-edit' );
$wgNamespaceProtection[NS_PROJMGMT] = array( 'projmgmt-edit' );
$wgNamespaceProtection[NS_ISSUE] = array( 'issue-edit' );
$wgNamespaceProtection[NS_GUIDE] = array( 'guide-edit' );
$wgNamespaceProtection[NS_ORG] = array( 'org-edit' );

# Namespace alias
$wgNamespaceAliases['SC'] = NS_PROJECT;
$wgNamespaceAliases['ST'] = NS_PROJECT_TALK;
$wgNamespaceAliases['H'] = NS_HELP;
$wgNamespaceAliases['T'] = NS_TEMPLATE;
$wgNamespaceAliases['CAT'] = NS_CATEGORY;
$wgNamespaceAliases['CL'] = NS_COMMLINK;
$wgNamespaceAliases['U'] = NS_UPDATE;

$wgVisualEditorAvailableNamespaces = array(
  NS_MAIN     	=> true,
  NS_USER     	=> true,
  NS_HELP     	=> true,
  NS_PROJECT 	=> true,
  NS_COMMLINK 	=> true,
  NS_PROJMGMT 	=> true,
  NS_ISSUE    	=> true,
  NS_GUIDE    	=> true,
  NS_ORG      	=> true,
  NS_UPDATE     => true
);

$wgContentNamespaces = [ NS_MAIN, NS_GUIDE, NS_COMMLINK, NS_UPDATE ];

#=============================================== Permissions ===============================================
$wgAutopromote = array(
  "autoconfirmed" => array( "&",
    array( APCOND_EDITCOUNT, &$wgAutoConfirmCount ),
    array( APCOND_AGE, &$wgAutoConfirmAge ),
    APCOND_EMAILCONFIRMED,
  ),
  "Trusted" => array( "&",
    array( APCOND_EDITCOUNT, 300),
    array( APCOND_INGROUPS, "Verified"),
  ),
);

#all
$wgGroupPermissions['*']['createaccount'] = true;
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['createpage'] = false;
$wgGroupPermissions['*']['writeapi'] = true;
$wgGroupPermissions['*']['createtalk'] = false;

#user
$wgGroupPermissions['user']['edit'] = true;
$wgGroupPermissions['user']['purge'] = false;
$wgGroupPermissions['user']['createpage'] = false;
$wgGroupPermissions['user']['createtalk'] = false;
$wgGroupPermissions['user']['minoredit'] = false;
$wgGroupPermissions['user']['move'] = false;
$wgGroupPermissions['user']['movefile'] = false;
$wgGroupPermissions['user']['move-categorypages'] = false;
$wgGroupPermissions['user']['move-rootuserpages'] = false;
$wgGroupPermissions['user']['move-subpages'] = false;
$wgGroupPermissions['user']['reupload'] = false;
$wgGroupPermissions['user']['reupload-own'] = false;
$wgGroupPermissions['user']['guide-edit'] = true;
$wgGroupPermissions['user']['oathauth-enable'] = true;

#ORG Editor
$wgGroupPermissions['ORG-Editor']['org-edit'] = true;

#autoconfirmed
$wgAutoConfirmAge = 86400*3; // three days
$wgAutoConfirmCount = 20;
$wgGroupPermissions['autoconfirmed']['upload_by_url'] = true;
$wgGroupPermissions['autoconfirmed']['createpage'] = true;
$wgGroupPermissions['autoconfirmed']['createtalk'] = true;

#verified
$wgGroupPermissions['Verified'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['Verified']['skipcaptcha'] = true;
$wgGroupPermissions['Verified']['purge'] = true;
$wgGroupPermissions['Verified']['reupload'] = true;
$wgGroupPermissions['Verified']['reupload-own'] = true;
$wgGroupPermissions['Verified']['minoredit'] = true;

#translator
$wgGroupPermissions['Translator']['translate'] = true;
$wgGroupPermissions['Translator']['translate-messagereview'] = true;

#trusted
$wgGroupPermissions['Trusted'] = $wgGroupPermissions['Verified'];
$wgGroupPermissions['Trusted']['patrol'] = true;
$wgGroupPermissions['Trusted']['move'] = true;
$wgGroupPermissions['Trusted']['movefile'] = true;
$wgGroupPermissions['Trusted']['move-categorypages'] = true;
$wgGroupPermissions['Trusted']['writeapi'] = true;
$wgGroupPermissions['Trusted']['sendemail'] = true;
$wgGroupPermissions['Trusted']['commlink-edit'] = true;
$wgGroupPermissions['Trusted']['issue-edit'] = true;
$wgGroupPermissions['Trusted']['projmgmt-edit'] = true;
$wgGroupPermissions['Trusted']['move-subpages'] = true;

#editor
$wgGroupPermissions['Editor'] = $wgGroupPermissions['Trusted'];
$wgAddGroups['Editor'] = array( 'Verified', 'Translator', 'ORG-Editor' );
$wgGroupPermissions['Editor']['template-edit'] = true;
$wgGroupPermissions['Editor']['rollback'] = true;
$wgGroupPermissions['Editor']['protect'] = true;
$wgGroupPermissions['Editor']['editprotected'] = true;
$wgGroupPermissions['Editor']['suppressredirect'] = true;
$wgGroupPermissions['Editor']['autopatrol'] = true;
$wgGroupPermissions['Editor']['checkuser'] = true;
$wgGroupPermissions['Editor']['translate-proofr'] = true;
$wgGroupPermissions['Editor']['translate-manage'] = true;
$wgGroupPermissions['Editor']['translate'] = true;
$wgGroupPermissions['Editor']['pagetranslation'] = true;
$wgGroupPermissions['Editor']['translate-groupreview'] = true;
$wgGroupPermissions['Editor']['delete'] = true;
$wgGroupPermissions['Editor']['bigdelete'] = true;
$wgGroupPermissions['Editor']['deletedhistory'] = true;
$wgGroupPermissions['Editor']['deletedtext'] = true;
$wgGroupPermissions['Editor']['block'] = true;
$wgGroupPermissions['Editor']['undelete'] = true;
$wgGroupPermissions['Editor']['mergehistory'] = true;
$wgGroupPermissions['Editor']['browsearchive'] = true;
$wgGroupPermissions['Editor']['noratelimit'] = true;
$wgGroupPermissions['Editor']['move-rootuserpages'] = true;
$wgGroupPermissions['Editor']['org-edit'] = true;

#sysop
$wgGroupPermissions['sysop'] = $wgGroupPermissions['Editor'];
$wgGroupPermissions['sysop']['userrights'] = true;
$wgGroupPermissions['sysop']['siteadmin'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;
$wgGroupPermissions['sysop']['nuke'] = true;
$wgGroupPermissions['sysop']['editinterface'] = true;
$wgGroupPermissions['sysop']['delete'] = true;
$wgGroupPermissions['sysop']['renameuser'] = true;
$wgGroupPermissions['sysop']['import'] = true;
$wgGroupPermissions['sysop']['importupload'] = true;

#=============================================== Footer ===============================================

$wgFooterIcons = [
    "poweredby" => [
        "mediawiki" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-mediawiki.svg",
            "url" => "https://www.mediawiki.org",
            "alt" => "Powered by MediaWiki",
            "height" => "42",
            "width" => "127",
        ]
    ],
/*
    "monitoredby" => [
          "wikiapiary" => [
              "src" => "$wgResourceBasePath/resources/assets/badge-wikiapiary.svg",
              "url" => "https://wikiapiary.com/wiki/The_Star_Citizen_Wiki",
              "alt" => "Monitored By Wikiapiary",
              "height" => "54",
              "width" => "95",
          ]
    ],
*/
/*
  "gdprcompliance" => [
        "gdpr" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-gdpr.svg",
            "url" => "https://gdpr.eu",
            "alt" => "GDPR compliant",
	    "height" => "50",
            "width" => "50",
        ]
    ],
*/
    "copyright" => [
        "copyright" => [
        "src" => "$wgResourceBasePath/resources/assets/badge-ccbysa.svg",
        "url" => $wgRightsUrl,
        "alt" => $wgRightsText,
	"height" => "50",
        "width" => "110",
      ]
    ],
    "madeby" => [
          "thecommunity" => [
              "src" => "$wgResourceBasePath/resources/assets/badge-starcitizencommunity.svg",
              "url" => "https://robertsspaceindustries.com",
              "alt" => "Made by the community",
	      "height" => "50",
              "width" => "50",
          ]
    ],
    "partof" => [
        "starcitizentools" => [
            "src" => "$wgResourceBasePath/resources/assets/badge-starcitizentools.svg",
            "url" => "https://starcitizen.tools",
            "alt" => "Part of Star Citizen Tools",
	    "height" => "50",
            "width" => "50",
        ]
    ]
];

# Add links to footer
$wgHooks['SkinAddFooterLinks'][] = function ( $sk, $key, &$footerlinks ) {
	$rel = 'nofollow noreferrer noopener';

	if ( $key === 'places' ) {
		$footerlinks['cookiestatement'] = Html::element(
			'a',
			[ 
				'href' => $sk->msg( 'cookiestatementpage' )->escaped(),
				'title' => $sk->msg( 'cookiestatementpage' )->text()
			],
			$sk->msg( 'cookiestatement' )->text()
		);
		$footerlinks['statuspage'] = Html::element(
			'a',
			[
				'href' => 'https://status.starcitizen.tools',
				'rel' => $rel
			],
			$sk->msg( 'footer-statuspage' )->text()
		);
		$footerlinks['github'] = Html::element(
			'a',
			[
				'href' => 'https://github.com/StarCitizenTools/mediawiki',
				'rel' => $rel
			],
			$sk->msg( 'footer-github' )->text()
		);
		$footerlinks['patreon'] = Html::element(
			'a',
			[
				'href' => 'https://www.patreon.com/starcitizentools',
				'rel' => $rel
			],
			$sk->msg( 'footer-patreon' )->text()
		);
	}
};

#============================== Final External Includes ===============================================

require_once("/home/www-data/external_includes/misc_server_settings.php");

# Override multilingual interface messages
#$wgHooks['MessageCache::get'][] = function ( &$key ) {
#    $keys = [
#            'badaccess-groups',
#            'citizen-footer-desc',
#            'citizen-footer-tagline',
#            'copyright',
#            'noarticletext',
#            'noarticletext-nopermission',
#            'tagline',
#    ];
#
#    if ( in_array( $key, $keys, true ) ) {
#            $key = "i18n-$key";
#    }
#};
