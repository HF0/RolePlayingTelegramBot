<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Repository\TelegramUserRepository;
use RolBot\Config\Configuration;
use RolBot\Telegram\Bot\BotRequestProcessor;
use Slim\Http\Uri;

$urlKey = Configuration::get('urlKey');
$app->group('/telegramhook' . $urlKey, function () use ($app) {
    $app->post('/hook.php', function (Request $request, Response $response) {
        $params = $this->request->getQueryParams();
        $secret = Configuration::get('queryParamSecret');
        if (!array_key_exists('secret', $params) || ($params['secret'] != $secret)) {
            return $response->withStatus(404);
        }
        $bot = new BotRequestProcessor(
            Configuration::get('bot_api_key'),
            Configuration::get('bot_username')
        );
        $result = $bot->run();
    })->setName('telegramHook');

    $app->get('/sethook', function (Request $request, Response $response) use ($app) {
        if ($request->getUri()->getScheme() !== 'https') {
            return "Https must be used to set hook";
        }
        $hookRelativeUrl = $app->getContainer()->get('router')->pathFor('telegramHook');
        $hookAbsoluteUrl = $request->getUri()->withPath($hookRelativeUrl);
        $secret = Configuration::get('queryParamSecret');
        $hookAbsoluteUrl .= "?secret={$secret}";

        $msg = "Setting webhook to {$hookAbsoluteUrl}. ";

        $bot = new BotRequestProcessor(
            Configuration::get('bot_api_key'),
            Configuration::get('bot_username')
        );
        $resultMessage = $bot->setWebHook($hookAbsoluteUrl);
        $msg .= "Result: {$resultMessage}";
        return $msg;
    })->setName('sethook');

    $app->get('/unsethook', function (Request $request, Response $response) {
        if ($request->getUri()->getScheme() !== 'https') {
            return "Https must be used to set hook";
        }
        $bot = new BotRequestProcessor(
            Configuration::get('bot_api_key'),
            Configuration::get('bot_username')
        );
        return $bot->unsetWebHook();
    })->setName('unsethook');
});
