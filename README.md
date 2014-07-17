silex-doctrine-pagination
=========================
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BI0R0B0T/silex-doctrine-pagination/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BI0R0B0T/silex-doctrine-pagination/?branch=master)

Simple pagination provider for [Silex micro-framework] (http://silex.sensiolabs.org/)
## Requirements

- PHP >= 5.3.3
- doctrine/dbal 2.2.*

## Usage

```php
$app->register(new \SilexDoctrinePagination\PaginationServiceProvider);
```
