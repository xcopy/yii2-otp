# Yii2 OTP

## Installation

The preferred way to install the extension is through [composer](http://getcomposer.org/download/).

Add to the `require` section of your `composer.json` file:

```
"xcopy/yii2-otp": "dev-main"
```

Add to the `repositories` section of your `composer.json` file:

```json
{
   "type": "vcs",
   "url": "https://github.com/xcopy/yii2-otp"
}
```

## Migrations

Run

```sh
php yii migrate -p @vendor/xcopy/yii2-otp/src/migrations
```

to add `otp`, `otp_expiry` and `otp_token` columns to the identity object table (commonly `user`).

and re-generate identity object model (commonly `app\models\User`).

## Configuration

```php
'bootstrap' => [
    // ...
    'otp'
],
'modules' => [
    // ...
    'otp' => [
        'class' => 'xcopy\otp\Module',
        // 'duration' => '5 minutes', // optional, defaults to "5 minutes"
        // 'length' => 6, // optional, defaults to "6"
        // 'userLoginDuration' => 3600 * 24 // optional, defaults to "0" (applicable only if \yii\web\User::$enableAutoLogin is set to `true`)
    ],
],
'params' => [
    'senderEmail' => 'Example',
    'senderName' => 'noreply@example.com',
],
```

### Email template

Create new `otp.php` email template file in the `Yii::$app->mailer->viewPath` (commonly `@app/mail`), where a variable with the OTP value will be provided.

Example:

```php
<?php /** @var string $otp */ ?>
<p>Hi,</p>
<p>Please use the verification code below to sign in.</p>
<p><?= $otp ?></p>
```
