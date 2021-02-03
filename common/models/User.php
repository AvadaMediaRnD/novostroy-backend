<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use backend\models\Role;
use yii\helpers\ArrayHelper;
use backend\models\RoleAccessToController;

/**
 * User model
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $birthdate
 * @property string $phone
 * @property string $viber
 * @property string $telegram
 * @property string $description
 * @property string $image
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * 
 * @property House[] $houses
 */
class User extends \common\models\ZModel implements IdentityInterface
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED = 1;
    const STATUS_DELETED = 0;
        
    const ROLE_ADMIN = 10;
    const ROLE_FIN_DIRECTOR = 8;
    const ROLE_ACCOUNTANT = 6;
    const ROLE_SALES_MANAGER = 4;
    const ROLE_MANAGER = 2;
    const ROLE_VIEWER_FLAT = 3;
    const ROLE_DEFAULT = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'status'], 'integer'],
            [['email', 'firstname', 'middlename', 'lastname', 'birthdate', 'phone', 'viber', 'telegram', 'image'], 'string', 'max' => 255],
            [['description'], 'string'],
            ['status', 'default', 'value' => self::STATUS_DISABLED],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DISABLED, self::STATUS_DELETED]],
            
            ['role', 'default', 'value' => self::ROLE_DEFAULT],
            ['role', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_FIN_DIRECTOR, self::ROLE_ACCOUNTANT, self::ROLE_SALES_MANAGER, self::ROLE_MANAGER, self::ROLE_VIEWER_FLAT, self::ROLE_DEFAULT]],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete() {
        if ($this->status != self::STATUS_DELETED) {
            return $this->changeStatus(self::STATUS_DELETED);
        }
        // return parent::delete();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['and', ['id' => $id], ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_DISABLED]]])->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['auth_key' => $token, 'status' => self::STATUS_ACTIVE, 'role' => self::ROLE_ADMIN])->one();
    }
    
    /**
     * Use this method for autologin functionality. 
     * $token must be a substring of 'auth_key'
     * @param string $token
     * @return static|null
     */
    public static function findIdentityByAutoLoginToken($token)
    {
        return static::find()->where(['and', ['like', 'auth_key', $token], ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_DISABLED]]])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['and', ['email' => $username], ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_DISABLED]]])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()->where([
            'and',
            ['password_reset_token' => $token],
            ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_DISABLED]],
        ])->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouses()
    {
        return $this->hasMany(House::className(), ['id' => 'house_id'])
            ->viaTable('user_house', ['user_id' => 'id']);
    }
    
    /**
     * @return array
     */
    public function getHouseIds()
    {
        if ($this->role == static::ROLE_ADMIN) {
            return ArrayHelper::getColumn(House::find()->asArray()->all(), 'id');
        }
        return ArrayHelper::getColumn(UserHouse::find()->where(['user_id' => $this->id])->asArray()->all(), 'house_id');
    }
    
    /**
     * @return string
     */
    public function getFullname()
    {
        $nameParts = [];
        if ($this->lastname) {
            $nameParts[] = $this->lastname;
        }
        if ($this->firstname) {
            $nameParts[] = $this->firstname;
        }
        if ($this->middlename) {
            $nameParts[] = $this->middlename;
        }
        return $nameParts ? implode(' ', $nameParts) : null;
    }
    
    /**
     * @return array
     */
    public static function getOptions($roles = [])
    {
        return \yii\helpers\ArrayHelper::map(static::find()->andFilterWhere(['in', 'role', $roles])->all(), 'id', 'fullname');
    }
    
    /**
     * Status options for select
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('model', 'Активен'),
            static::STATUS_DISABLED => Yii::t('model', 'Неактивен'),
            static::STATUS_DELETED => Yii::t('model', 'Удален'),
        ];
    }

    /**
     * @param integer|null $status
     * @return string|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status === null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'text-default';
        if ($status == static::STATUS_ACTIVE) {
            $itemClass = 'text-green';
        } elseif ($status == static::STATUS_DISABLED) {
            $itemClass = 'text-red';
        } elseif ($status == static::STATUS_DELETED) {
            $itemClass = 'text-muted';
        }
        return '<span class="'.$itemClass.'">'.$statusLabel.'</span>';
    }
    
    /**
     * Role options for select
     * @return array
     */
    public static function getRoleOptions()
    {
        return [
            static::ROLE_ADMIN => Yii::t('model', 'Директор'),
            static::ROLE_FIN_DIRECTOR => Yii::t('model', 'Фин. директор'),
            static::ROLE_ACCOUNTANT => Yii::t('model', 'Бухгалтер'),
            static::ROLE_SALES_MANAGER => Yii::t('model', 'Рук. продаж'),
            static::ROLE_MANAGER => Yii::t('model', 'Менеджер'),
            static::ROLE_VIEWER_FLAT => Yii::t('model', 'Просмотр квартир'),
            static::ROLE_DEFAULT => Yii::t('model', 'Не выбрано'),
        ];
    }

    /**
     * @param integer|null $role
     * @return string|null
     */
    public function getRoleLabel($role = null)
    {
        $role = $role === null ? $this->role : $role;
        $options = static::getRoleOptions();
        return isset($options[$role]) ? $options[$role] : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleObject()
    {
        return $this->hasOne(Role::className(), ['id' => 'role']);
    }

    public function hasAccessToController($controllerName) {
        $allowsRolesArray = ArrayHelper::getColumn(RoleAccessToController::findAll(['controller_name' => $controllerName]), function ($model) {
            return (string)$model->role->name;
        });

        $allowsRolesArray[] = 'admin';

        return   in_array((string)$this->roleObject->name , $allowsRolesArray);
    }
    
    public function getAvatar()
    {
        if ($this->image && file_exists(Yii::getAlias('@frontend/web' . $this->image))) {
            return $this->image;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * Send invite to User email. Url has token param for autologin
     */
    public function sendInvite()
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/', 'token' => $this->auth_key]);
        $email = $this->email;
        $title = 'Вход в систему ' . Yii::$app->name;
        $message = 'Для входа в систему "' . Yii::$app->name . '" перейдите по ссылке:'
            . "\r\n \r\n" . '<a href="' . $url . '">' . $url . '</a>';
        
        \Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($email)
            ->setSubject($title)
            ->setTextBody(strip_tags($message))
            ->setHtmlBody(nl2br($message))
            ->send();
    }
    
    /**
     * 
     * @param integer $length
     * @return string
     */
    public function generatePassword($length = 8)
    {
        $this->setPassword(static::generatePasswordStatic($length));
    }
    
    /**
     * 
     * @param integer $length
     * @return string
     */
    public static function generatePasswordStatic($length = 8)
    {
        $string = substr(md5(time()), 0, $length);
        return $string;
    }
    
    /**
     * @return string
     */
    public function generateEmail($host = '')
    {
        if (!$host) {
            $host = Yii::$app->request->hostName ?: 'email.temp';
        }
        $this->email = 'u' . time() . (rand(1000, 9999)) . '@' . $host;
    }
    
    /**
     * Create commission invoice model for flat. If commission exists it can be updated
     * @param Flat $flat
     * @param Cashbox $cashbox if not set usd will be used
     * @param boolean $updateIfExists
     * @return Invoice|null
     */
    public function createCommissionForFlat($flat, $cashbox = null, $updateIfExists = false)
    {
        if (!$cashbox) {
            $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
        }
        $query = Invoice::find()->where([
            'user_id' => $this->id, 
            'flat_id' => $flat->id,
            'type' => Invoice::TYPE_OUTCOME,
            'article_id' => Article::getIdCommissionManager()
        ]);
        $commissionUserExists = $query->exists();
        if (!$commissionUserExists) {
            $invoice = new Invoice();
            $invoice->user_id = $this->id;
            $invoice->flat_id = $flat->id;
            $invoice->generateUid();
            $invoice->uid_date = date('Y-m-d', $invoice->uid_date ? strtotime($invoice->uid_date) : time());
            $invoice->status = Invoice::STATUS_COMPLETE;
            $invoice->type = Invoice::TYPE_OUTCOME;
            $invoice->article_id = Article::getIdCommissionManager();
            $invoice->cashbox_id = $cashbox->id;
            $invoice->rate = $cashbox->rate;
            $invoice->price = $flat->getCommissionManagerPrice();
            $invoice->description = 'Выплата комиссионных по квартире';
            if ($invoice->save()) {
                //// LOG
                $invoice->saveLog(__METHOD__ . ' flat:' . $flat->id . ' user:' . $this->id);
                //// END LOG
                return $invoice;
            }
        } elseif ($updateIfExists) {
            $invoice = $query->one();
            $invoice->uid_date = date('Y-m-d', $invoice->uid_date ? strtotime($invoice->uid_date) : time());
            $invoice->cashbox_id = $cashbox->id;
            $invoice->rate = $cashbox->rate;
            $invoice->price = $flat->getCommissionManagerPrice();
            if ($invoice->save()) {
                return $invoice;
            }
        }
        return null;
    }
}
