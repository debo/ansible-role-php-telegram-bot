<?php

use Dotenv\Dotenv;
use Longman\TelegramBot\Exception;
use Longman\TelegramBot\TelegramLog;
use NPM\TelegramBotManager\BotManager;

// Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';
(new Dotenv(__DIR__ . '/..'))->load();

$debug = (bool) getenv('TG_DEBUG');

try {
    // Vitals!
    $params = [
        'api_key' => getenv('TG_API_KEY'),
    ];
    foreach (['bot_username', 'secret'] as $extra) {
        if ($param = getenv('TG_' . strtoupper($extra))) {
            $params[$extra] = $param;
        }
    }

    // Database connection.
    if (getenv('TG_DB_HOST')) {
        $params['mysql'] = [
            'host'     => getenv('TG_DB_HOST'),
            'user'     => getenv('TG_DB_USER'),
            'password' => getenv('TG_DB_PASSWORD'),
            'database' => getenv('TG_DB_DATABASE'),
        ];
    }

    // Optional extras.
    $extras = ['admins', 'botan', 'commands', 'cron', 'limiter', 'logging', 'paths', 'valid_ips', 'webhook'];
    foreach ($extras as $extra) {
        if ($param = getenv('TG_' . strtoupper($extra))) {
            $params[$extra] = json_decode($param, true);
        }
    }

    $bot = new BotManager($params);
    $bot->run();
} catch (Exception\TelegramException $e) {
    TelegramLog::error($e);
    $debug && print $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    TelegramLog::error($e);
    $debug && print $e->getMessage() . PHP_EOL;
} catch (\Throwable $e) {
    TelegramLog::error($e);
    $debug && print $e->getMessage() . PHP_EOL;
}
