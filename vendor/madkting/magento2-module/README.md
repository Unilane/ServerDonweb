Madkting - Magento 2 Module
=========

Connect and synchronize your Magento 2 store with the biggest online Marketplaces in LATAM.

> NOTE: This module is only for Magento 2.1+ versions, if you want it for Magento 2.0.x you can find it in https://bitbucket.org/madkting/magento-2.0

Installation
---------

1. Add the dependency in your composer config

    ```
    composer require madkting/magento2-module
    ```

2. Enable it in Magento

    ```
    php bin/magento module:enable Madkting_Connect
    ```

3. Upgrade your Magento DB

    ```
    php bin/magento setup:upgrade
    ```

Updates
---------

1. Update the dependency in your composer config

    ```
    composer update madkting/magento2-module --with-dependencies
    ```

2. Upgrade your Magento DB

    ```
    php bin/magento setup:upgrade
    ```
