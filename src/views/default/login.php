<?php

/** @var yii\web\View $this */
/** @var xcopy\otp\models\LoginForm $model */

$this->title = Yii::t('xcopy/otp', 'Login');

echo $this->render('_form', compact('model'));
