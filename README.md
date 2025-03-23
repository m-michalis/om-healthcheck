# Openmage Healthcheck

An endpoint to healthcheck Openmage

## Features

- Check Aoe_Scheduler status
- Check if OM has updates
- Check if cache status is enabled

## Installation

### Composer

```json
{
    "minimum-stability": "dev",
    "require": {
        "m-michalis/om-healthcheck": "0.1.*"
    }
}
```

## Usage

### Start with
`https://example.com/ic_health/check`

#### CRON
item:
- Name: `{#DOMAIN}: Cron last heartbeat`
- Key: `om_health.cron_heartbeat[{#DOMAIN}]`

trigger information:
```
last(/InternetCode OpenMage Healthcheck/om_health.cron_heartbeat[{#DOMAIN}])>5
```

trigger high:
```
last(/InternetCode OpenMage Healthcheck/om_health.cron_heartbeat[{#DOMAIN}])>20
```

trigger CRITICAL:
```
last(/InternetCode OpenMage Healthcheck/om_health.cron_heartbeat[{#DOMAIN}])<0
```

#### version
trigger information:
```
jsonpath(last(/InternetCode OpenMage Healthcheck/om_health.data[{#DOMAIN}]),"$.current_version")!=
jsonpath(last(/InternetCode OpenMage Healthcheck/om_health.data[{#DOMAIN}]),"$.latest_version")
```


#### cache
item:
- Name: `{#DOMAIN}: Cache status`
- Key: `om_health.cache_status[{#DOMAIN}]`

trigger information:
```
last(/InternetCode OpenMage Healthcheck/om_health.cache_status[{#DOMAIN}]) != ''
```


## Compatibility (tested with)
- OpenMage 20.0.x
- MariaDB 11.4
- Magento 1.9.x

## License
This module is released under the GPL-3.0 License.
