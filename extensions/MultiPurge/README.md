# MediaWiki MultiPurge extension

Allows purging of pages for multiple services in a defined order.

Based on https://phabricator.wikimedia.org/T216225#5335375

```
For a custom CDN purger:

    Enable $wgUseCDN so that CdnCacheUpdate runs. (Keep these off $wgCdnReboundPurgeDelay, $wgCdnServers, and $wgHTCPRouting).
```

## Configuration Options

| Variable                               | Default Value    | Description                                                                                                                                             |
|----------------------------------------|------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$wgMultiPurgeCloudFlareZoneId`        | null             | String - Zone ID the Wiki Domain is contained in                                                                                                        |
| `$wgMultiPurgeCloudFlareApiToken`      | null             | String - API Token found in your dashboard                                                                                                              |
| `$wgMultiPurgeVarnishServers`          | null             | String/Array - Array of URLs pointing to your Varnish Servers. Can be IPs                                                                               |
| `$wgMultiPurgeEnabledServices`         | null             | Array - List of enabled services. Possible values are 'Cloudflare', 'Varnish'                                                                           |
| `$wgMultiPurgeServiceOrder`            | null             | Array - List of service purge order. Possible values are 'Cloudflare', 'Varnish'. Example: ['Varnish', 'Cloudflare'] purges varnish, then cloudflare    |


## Special Page
MultiPurge adds a special page for sysops which allows purging of `load.php` urls.  
The page can be found at Special:PurgeResources.  

Only users with `editinterface` permissions can access this page.  

The page works by requesting the actual html output of a given title, and parsing all `load.php` calls.  
All found links can then be selected to be purged.
