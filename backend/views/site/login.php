<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use Yii,
    yii\helpers\Html,
    yii\bootstrap\ActiveForm,
    yii\helpers\Url,
    yii\web\View;

$this->registerJs("
    $('.login-box-body input[type=\"checkbox\"]').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'icheckbox_flat-blue'
    });
", View::POS_READY);

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#!">
            <img src="<?= Url::to('/admin-lte/dist/img/logo_min.png') ?>" alt="logo">
        </a>
    </div>

    <div class="nav-tabs-custom">
        <div class="login-box-body">
            <p class="login-box-msg">Вход в панель управления</p>
            <?php
            foreach (Yii::$app->session->getAllFlashes() as $type => $message):
                ?>
                <div class="alert alert-<?= $type ?>" role="alert"><?= $message ?></div>
                <?php
            endforeach
            ?>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?=
                    $form->field($model, 'username', ['template' => '
                        <div class="form-group has-feedback">
                            <div class="input-group col-sm-12">
                                {input}
                                <span class="fa fa-sign-in form-control-feedback"></span>
                            </div>
                        </div>'])->textInput(['autofocus' => true])
                    ->input('text', ['placeholder' => 'Логин'])
            ?>

            <?=
                    $form->field($model, 'password', ['template' => '
                        <div class="form-group has-feedback">
                            <div class="input-group col-sm-12">
                                {input}
                                <span class="fa fa-lock form-control-feedback"></span>
                            </div>
                        </div>'])->passwordInput()
                    ->input('password', ['placeholder' => 'Пароль'])
            ?>
            <div class="row">

                <div class="col-xs-12">
                    <?= $form->field($model, 'rememberMe', ['options' => ['class' => 'checkbox icheck'], 'enableClientValidation' => false])->checkbox()->label('Запомнить меня', ['style' => 'padding-left: 0px;']) ?>
                </div>

                <div class="col-xs-12">
                    <?= Html::submitButton('Вход', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                    <!-- <button type="submit" class="btn btn-primary btn-block">Вход</button>-->
                    <!-- <a href="http://front.avada-soft.com/myhouse24account" class="btn btn-primary btn-block">Вход</a>-->
                    <br/>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <!--            <a href="#!">Забыли пароль?</a><br>
                        <a href="#!">Регистрация</a>-->
        </div>
    </div>
</div>