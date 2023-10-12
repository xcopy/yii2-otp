<?php

use yii\bootstrap5\{ActiveForm, Html};

/** @var yii\web\View $this */
/** @var xcopy\otp\models\LoginForm $model */

$isLoginScenario = $model->isLoginScenario();
$isRequestScenario = $model->isRequestScenario();

?>

<div class="d-flex align-items-center justify-content-center h-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-3"></div>
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <?php $form = ActiveForm::begin() ?>
                    <div class="card-body">
                        <?php echo $form->field($model, 'email')->textInput([
                            'autofocus' => $isRequestScenario,
                            'readonly' => $isLoginScenario,
                            'class' => $isLoginScenario ? 'form-control-plaintext' : 'form-control'
                        ]);

                        if ($isLoginScenario) {
                            echo $form->field($model, 'otp')->textInput([
                                'autofocus' => $isLoginScenario,
                                'placeholder' => Yii::t('xcopy/otp', 'Enter OTP')
                            ]);

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
