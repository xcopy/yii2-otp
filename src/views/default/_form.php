<?php

use xcopy\otp\Module;
use yii\bootstrap5\{ActiveForm, Html};

/** @var yii\web\View $this */
/** @var xcopy\otp\models\LoginForm $model */
/** @var yii\web\IdentityInterface|null $user */

$isLoginScenario = $model->isLoginScenario();
$isRequestScenario = $model->isRequestScenario();
$module = Module::getInstance();

if ($isLoginScenario) {
    $remaining = strtotime($user->otp_expiry) - time();
    $this->registerJs(<<<JS
const el = document.getElementById('timer');
const timer = function (remaining) {
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;

    m = m < 10 ? '0' + m : m;
    s = s < 10 ? '0' + s : s;

    el.innerHTML = m + ':' + s;

    remaining -= 1;
  
    if (remaining >= 0) {
        setTimeout(function () {
            timer(remaining);
        }, 1000);
    }
}

el && timer('$remaining');
JS
    );
}

?>

<div class="d-flex align-items-center justify-content-center h-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-3"></div>
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header"><?= $this->title ?></div>
                    <?php $form = ActiveForm::begin(['validateOnBlur' => false]) ?>
                    <div class="card-body">
                        <?php echo Html::tag(
                            'div',
                            $isRequestScenario
                                ? Yii::t('xcopy/otp', 'A One Time Password (OTP) will be sent to the email address you provide.')
                                : Yii::t('xcopy/otp', 'Enter the {0}-digit One Time Password (OTP) that was sent. It expires in {1}.', [
                                    $module->length,
                                    Html::tag('strong', '', ['id' => 'timer'])
                                ]),
                            ['class' => 'text-secondary text-center small mb-3']
                        );

                        echo $form->field($model, 'email')
                            ->textInput([
                                'autofocus' => $isRequestScenario,
                                'readonly' => $isLoginScenario,
                                'class' => $isLoginScenario ? 'form-control-plaintext' : 'form-control'
                            ])
                            ->label($isRequestScenario);

                        if ($isLoginScenario) {
                            echo $form->field($model, 'otp')
                                ->textInput([
                                    'autofocus' => true,
                                    'inputmode' => 'numeric',
                                    'placeholder' => Yii::t('xcopy/otp', 'Enter OTP'),
                                ])
                                ->label(false);

                            if (Yii::$app->user->enableAutoLogin) {
                                echo $form->field($model, 'rememberMe')->checkbox();
                            }
                        } ?>

                        <div class="d-grid">
                            <?= Html::button(
                                $isRequestScenario
                                    ? Yii::t('xcopy/otp', 'Send OTP')
                                    : Yii::t('xcopy/otp', 'Login'),
                                [
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary',
                                ]
                            ) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-3"></div>
        </div>
    </div>
</div>
