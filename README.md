# EventLogger

Version: 1.0

A simple API wrapper for EventLog (<http://eventlogapp.com>).

## Usage

#### Basic Usage

```php
$logger = new EventLogger('foo@bar.com', 'password', 'your\_app\_api\_key');
$logger->log('Something went horribly wrong!');
```

#### Event Types

It will default to being an Error Message.  You can use any of the default Event Types by using the following constants as the second parameter to `log()`:

```php
EventLogger::ERROR
EventLogger::WARNING
EventLogger::NOTICE
EventLogger::SUCCESS
EventLogger::GENERAL
```

**Usage**

```php
$logger->log('User Added!', EventLogger::SUCCESS);
```

You can also use any custom Event Type ID:

```php
$logger->log('What is the meaning of life?', 42);
```

## License

EventLogger is released under the MIT License and is copyrighted 2011 Dan Horrigan.