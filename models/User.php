<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $username
 * @property string $auth_key
 * @property string $email_confirm_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status

 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            [
                'username',
                'unique',
                'targetClass' => self::className(),
                'message'     => 'This username has already been taken.'
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'unique',
                'targetClass' => self::className(),
                'message'     => 'This email address has already been taken.'
            ],
            ['email', 'string', 'max' => 255],
            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'created_at'           => 'Создан',
            'updated_at'           => 'Редактирован',
            'username'             => 'Логин',
            'auth_key'             => 'Auth Key',
            'email_confirm_token'  => 'Email Confirm Token',
            'password_hash'        => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email'                => 'E-mail',
            'status'               => 'Статус',
        ];
    }

    public function getStatusName()
    {
        $statuses = self::getStatusesArray();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    public static function getStatusesArray()
    {
        return [
            self::STATUS_BLOCKED => 'Заблокирован',
            self::STATUS_ACTIVE  => 'Активные',
            self::STATUS_WAIT    => 'Ожидает',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::className(), ['lead_blocked_by' => 'id']);
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }

            return true;
        }

        return false;
    }
}
