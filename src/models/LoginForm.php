<?php

namespace xcopy\otp\models;

use xcopy\otp\Module;
use xcopy\otp\jobs\SendOtpJob;
use yii\base\Model;
use yii\db\{ActiveQueryInterface, Expression};
use yii\web\IdentityInterface;
use Yii;

/**
 * Class LoginForm
 *
 * @package xcopy\otp\models
 * @author Kairat Jenishev <kairat.jenishev@gmail.com>
 *
 * @property-read IdentityInterface|null $user
 */
class LoginForm extends Model
{
    /** @var string|null */
    public $email;

    /** @var string|null */
    public $otp;

    /** @var bool */
    public $rememberMe;

    /** @var IdentityInterface|null */
    private $_user;

    public const SCENARIO_LOGIN = 'login';
    public const SCENARIO_REQUEST = 'request';

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [
                'email',
                'required',
                'on' => [self::SCENARIO_LOGIN, self::SCENARIO_REQUEST]
            ],
            ['email', 'email'],
            /** @see validateEmail() */
            [
                'email',
                'validateEmail',
                'on' => [self::SCENARIO_LOGIN, self::SCENARIO_REQUEST]
            ],
            [
                'otp',
                'required',
                'on' => self::SCENARIO_LOGIN
            ],
            /** @see validateOtp() */
            [
                'otp',
                'validateOtp',
                'on' => self::SCENARIO_LOGIN
            ],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'E-mail'),
            'otp' => Yii::t('app', 'OTP'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    /**
     * Validates the email
     *
     * @param string $attribute the attribute currently being validated
     *
     * @return void
     */
    public function validateEmail(string $attribute): void
    {
        if (!$this->hasErrors()) {
            if (!$user = $this->user) {
                $this->addError($attribute, Yii::t('app', 'Incorrect email.'));
            } elseif (method_exists($user, 'validateUser')) {
                $user->validateUser();
                $user->hasErrors() and $this->addError($attribute, current($user->firstErrors));
            }
        }
    }

    /**
     * Validates the OTP
     *
     * @param string $attribute the attribute currently being validated
     *
     * @return void
     */
    public function validateOtp(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->user;

            if (!$user || !Yii::$app->security->validatePassword($this->otp, $user->otp)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect OTP.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        $module = Module::getInstance();

        if ($this->validate()) {
            $user = $this->user;

            $login = Yii::$app->user->login(
                $user,
                $this->rememberMe ? $module->userLoginDuration : 0
            );

            $login and $this->resetOtp($user);

            return $login;
        }

        return false;
    }

    /**
     * Generates and sends OTP to the recipient
     *
     * @return string|null
     */
    public function sendOtp(): ?string
    {
        $module = Module::getInstance();

        $otp = str_pad(mt_rand(0, str_repeat('9', $module->length)), $module->length, '0', STR_PAD_LEFT);

        $user = $this->user;

        if ($user->otp && time() < strtotime($user->otp_expiry)) {
            return $user->otp_token;
        }

        $user->otp = Yii::$app->security->generatePasswordHash($otp);
        $user->otp_expiry = date('Y-m-d H:i:s', strtotime($module->duration));
        $user->otp_token = Yii::$app->security->generateRandomString();

        if ($user->save()) {
            Yii::$app->queue->push(
                new SendOtpJob(['email' => $user->email, 'otp' => $otp])
            );

            return $user->otp_token;
        }

        return null;
    }

    /**
     * Resets OTP
     *
     * @param IdentityInterface $user
     *
     * @return bool
     */
    public function resetOtp(IdentityInterface $user): bool
    {
        $user->otp = null;
        $user->otp_expiry = null;
        $user->otp_token = null;

        return $user->save(false);
    }

    /**
     * Finds user by username
     *
     * @return IdentityInterface|null
     * @see user
     */
    public function getUser(): ?IdentityInterface
    {
        if ($this->_user === null) {
            /** @var ActiveQueryInterface $query */
            $query = call_user_func([Yii::$app->user->identityClass, 'find']);
            $query->andWhere(new Expression('lower(email) = :email', ['email' => strtolower($this->email)]));

            $this->_user = $query->one();
        }

        return $this->_user;
    }

    /**
     * @return bool
     */
    public function isLoginScenario() : bool
    {
        return $this->scenario == self::SCENARIO_LOGIN;
    }

    /**
     * @return bool
     */
    public function isRequestScenario() : bool
    {
        return $this->scenario == self::SCENARIO_REQUEST;
    }
}
