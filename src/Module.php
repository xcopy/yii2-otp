<?php

namespace xcopy\otp;

use Yii;
use yii\base\{BootstrapInterface, Module as BaseModule};

/**
 * Class Module
 *
 * @package xcopy\otp
 * @author Kairat Jenishev <kairat.jenishev@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface
{
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
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => $this->id . '/<action:[\w\-]+>',
                'route' => $this->id . '/default/<action>'
            ],
        ], false);

        $app->on('afterRequest', function () use ($app) {
            if ($app->user->isGuest) {
                if ($app->requestedRoute == trim($app->user->loginUrl, '/')) {
                    $app->response->redirect([$this->id]);
                }
            }
        });
    }
}
