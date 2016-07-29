# OpenID Web Client - Testing Authentication Strategy

Fully-tested with any OpenID server compliant, you only need a issuer URL. Try with ID Cultura and check for yourself how OpenID Connect specification works.

## Motivations

### OIDC Dynamic Registration

Following to OpenID specification you can self register a new client and save to session after successful register.

### Session Enabled

After register, the new generated Client ID and Client Secret will be saved to session and you could authenticate with this client.

###  Scopes and User Info

When you are testing authentication, you have an option to change scopes and user info you will want to access.

### Open Source

This project was build to test Login Cidadão OpenID capabilities. Thanks to @guilhermednt to help with his knowledge about OpenID.

## Requirements

* PHP >= 7
* Composer

## Setup

```
git clone git@github.com:lpirola/oidc-web-client.git
cd oidc-web-client
composer install
php -S localhost:8080 -t web web/index.php
```
