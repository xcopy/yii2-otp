<?php

namespace xcopy\otp;

use Yii;
use yii\base\{Application, BootstrapInterface, Module as BaseModule};
use yii\web\{Application as WebApplication, User};

/**
 * Class Module
 *
 * @package xcopy\otp
 * @author Kairat Jenishev <kairat.jenishev@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface
{
    /** @var int OTP length */
    public int $length = 6;

    /** @var string OTP expiry duration */
    public string $duration = '5 minutes';

    /**
     * @var int number of seconds that the user can remain in logged-in status
     * @see User::login()
     */
    public int $userLoginDuration = 0;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->i18n->translations['xcopy/otp'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/xcopy/yii2-otp/src/messages'
        ];
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        /** @var WebApplication $app */
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => $this->id . '/<action:[\w\-]+>',
                'route' => $this->id . '/default/<action>'
            ],
        ], false);

        $app->on(Application::EVENT_AFTER_REQUEST, function () use ($app) {
            if ($app->user->getIsGuest()) {
                if ($app->requestedRoute == trim($app->user->loginUrl, '/')) {
                    $app->response->redirect([$this->id]);
                    $app->end();
                }
            }
        });
    }
}
