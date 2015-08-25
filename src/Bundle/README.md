# PhraseanetBundle

The Phraseanet bundle provides [Symfony](http://symfony.com) integration for the [Phraseanet SDK](https://github.com/alchemy-fr/Phraseanet-PHP-SDK).

## Configuration

The bundle provides semantic configuration to simplify the bootstrap of the SDK.

### Minimal configuration required

In your `app/config/config.yml` file, add the following configuration block:

```
phraseanet:
  sdk:
    client-id: %phraseanet.client-id%
    secret: %phraseanet.secret%
    url: %phraseanet.instance-url%
    token: %phraseanet.token%
  cache:
    type: array
  recorder: false
```

And in your `app/config/parameters.yml` file, add the following parameters with the appropriate values:

```
parameters:
    phraseanet.client-id: YOUR_CLIENT_ID
    phraseanet.secret: YOUR_SECRET
    phraseanet.instance-url: INSTANCE_URL
    phraseanet.token: PHRASEANET_TOKEN
```

**Hint**: You need to create an application token in your Phraseanet account. Navigate to your account page on Phraseanet 
(on a Vagrant install for Phraseanet 3.8, the URL is [http://phraseanet-php54-nginx/developers/applications/](http://phraseanet-php54-nginx/developers/applications/)).

## Exposed services

The bundle exposes by default the following services:

- 

