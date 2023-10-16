<?php

/** @var yii\web\View $this */
/** @var xcopy\otp\models\LoginForm $model */
/** @var yii\web\IdentityInterface|null $user */

$this->title = Yii::t('xcopy/otp', 'Sign in');

echo $this->render('_form', compact('model', 'user'));
