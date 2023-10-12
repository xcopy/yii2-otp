<?php

namespace xcopy\otp\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;

/**
 * Class SendOtpJob
 *
 * @package xcopy\otp\jobs
 * @author Kairat Jenishev <kairat.jenishev@gmail.com>
 */
class SendOtpJob extends BaseObject implements JobInterface
{
    /** @var string */
    public string $email;

    /** @var string */
    public string $otp;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        return Yii::$app->mailer
            ->compose('otp', ['otp' => $this->otp])
            ->setSubject('OTP Verification - ' . Yii::$app->name)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($this->email)
            ->send();
    }
}
