<?php
namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * User form
 */
class UserForm extends Model
{
    public $id;
    public $firstname;
    public $lastname;
    public $middlename;
    public $birthdate;
    public $phone;
    public $viber;
    public $telegram;
    public $description;
    public $role;
    public $email;
    public $status;
    public $password;
    public $password2;
    
    public $image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['firstname', 'lastname', 'middlename', 'birthdate', 'phone', 'viber', 'telegram', 'email'], 'string', 'max' => 255],
            [['password', 'password2'], 'string', 'min' => 6, 'max' => 255],
            ['email', 'email'],
            ['email', 'required'],
            ['description', 'string'],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Этот логин уже используется.',
                'when' => function ($model) {
                    $user = User::find()->where(['email' => $model->email])->one();
                    return $user && ($user->id != Yii::$app->request->get('id'));
                }
            ],
            ['password', 'required', 'when' => function ($model) {
                $user = User::find()->where(['email' => $model->email])->one();
                return !$user;
            }, 'whenClient' => "function (attribute, value) {
                return $('#is_new_record').val() == '1';
            }", 'skipOnEmpty' => true],
            ['password2', 'required', 'when' => function ($model) {
                return $model->password != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#userform-password').val() != '';
            }"],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('model', 'Пароли не совпадают.')],
            ['status', 'default', 'value' => User::STATUS_ACTIVE],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DISABLED, User::STATUS_DELETED]],
            ['role', 'default', 'value' => User::ROLE_DEFAULT],
            ['role', 'in', 'range' => [User::ROLE_ADMIN, User::ROLE_FIN_DIRECTOR, User::ROLE_ACCOUNTANT, User::ROLE_SALES_MANAGER, User::ROLE_MANAGER, User::ROLE_VIEWER_FLAT, User::ROLE_DEFAULT]],
            ['image', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'email' => Yii::t('model', 'Email (логин)'),
            'firstname' => Yii::t('model', 'Имя'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'middlename' => Yii::t('model', 'Отчество'),
            'birthdate' => Yii::t('model', 'Дата рождения'),
            'phone' => Yii::t('model', 'Телефон'),
            'viber' => Yii::t('model', 'Viber'),
            'telegram' => Yii::t('model', 'Telegram'),
            'description' => Yii::t('model', 'Заметки'),
            'status' => Yii::t('model', 'Статус'),
            'role' => Yii::t('model', 'Роль'),
            'password' => Yii::t('model', 'Пароль'),
            'password2' => Yii::t('model', 'Повторить пароль'),
            'image' => Yii::t('model', 'Сменить изображение'),
        ];
    }

    /**
     * Save user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function save()
    {
        if ($this->validate()) {
            $user = User::findOne($this->id);
            if (!$user) {
                $user = new User();
            }
            
            $user->setAttributes($this->attributes);
            $user->birthdate = date('Y-m-d', strtotime($this->birthdate));
            
            if ($user->isNewRecord && !$this->password) {
                $this->password = User::generatePasswordStatic();
            }
            if ($this->password) {
                $user->setPassword($this->password);
            }
            
            if ($user->isNewRecord) {
                $user->generateAuthKey();
            }
            
            if ($user->save()) {
                // image
                $file = UploadedFile::getInstance($this, 'image');
                if ($file) {
                    $path = '/upload/User/' . $user->id . '/avatar.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $user->image = $path;
                        $user->save(false);
                        Yii::$app->glide->getServer()->deleteCache($path);
                    }
                }
                
                return $user;
            }
        }

        return null;
    }
    
    /**
     * 
     * @return type
     */
    public function getAvatar()
    {
        $model = User::find()->where(['email' => $this->email])->one();
        if (!$model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $model->getAvatar();
    }
    
    /**
     * 
     * @param User $user
     */
    private function sendNewPassword($user)
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/']);
        $email = $user->email;
        $title = 'Пароль для входа';
        $message = 'Ваш пароль для входа в кабинет "' . Yii::$app->name . '":'
            . "\r\n" . $this->password
            . "\r\n \r\n" . 'Ссылка для входа: <a href="' . $url . '">' . $url . '</a>';
        
        \Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($email)
            ->setSubject($title)
            ->setTextBody(strip_tags($message))
            ->setHtmlBody(nl2br($message))
            ->send();
    }
}
