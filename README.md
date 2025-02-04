# Openmage Healthcheck

An endpoint to healthcheck Openmage

## Features

- Check Aoe_Scheduler status
- Check if OM has updates
- Check if cache status is enabled

I encourage you to check `app/code/local/InternetCode/Feed/Model/Feed.php`

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

## Compatibility (tested with)
- OpenMage 20.0.x
- MariaDB 11.4
- Magento 1.9.x

## License
This module is released under the GPL-3.0 License.
