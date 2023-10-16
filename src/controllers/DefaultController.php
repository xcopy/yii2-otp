<?php

namespace xcopy\otp\controllers;

use xcopy\otp\models\LoginForm;
use yii\db\ActiveQueryInterface;
use yii\web\{BadRequestHttpException, Controller, IdentityInterface, Response};
use Yii;

/**
 * Class OtpController
 *
 * @package xcopy\otp\controllers
 * @author Kairat Jenishev <kairat.jenishev@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @return Response|string
     */
    public function actionIndex(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_REQUEST;

        if ($model->load($this->request->post()) && $model->validate()) {
            $token = $model->sendOtp();
            return $this->redirect(["/{$this->module->id}/login", 'token' => $token]);
        }

        return $this->render('index', compact('model'));
    }

    /**
     * @param string $token
     *
     * @return Response|string
     */
    public function actionLogin(string $token): Response|string
    {
        /** @var ActiveQueryInterface $query */
        $query = call_user_func([Yii::$app->user->identityClass, 'find']);
        $query->andWhere(['otp_token' => $token]);

        /** @var IdentityInterface|null $user */
        if (!$user = $query->one()) {
            throw new BadRequestHttpException(Yii::t('xcopy/otp', 'Invalid token.'));
        }

        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_LOGIN;

        if (time() > strtotime($user->otp_expiry)) {
            Yii::$app->session->setFlash('warning', Yii::t('xcopy/otp', 'OTP has expired.'));

            $model->resetOtp($user);

            return $this->redirect(["/{$this->module->id}/index"]);
        }

        $model->email = $user->email;

        if ($model->load($this->request->post()) && $model->login()) {
            return $this->redirect([Yii::$app->homeUrl]);
        }

        return $this->render('login', compact('model', 'user'));
    }
}
