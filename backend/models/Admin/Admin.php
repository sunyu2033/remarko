<?php

namespace backend\models\Admin;

use phpDocumentor\Reflection\Types\Static_;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "sys_admin".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $groupId
 * @property string $name
 * @property string $description
 * @property string $visitId
 * @property string $lastOnlineTime
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    const STATUS_NORMAL = 0;
    const STATUS_PAUSE = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    public function getAdminGroup ()
    {
        return $this->hasOne(AdminGroup::className(), ['id'=>'groupId']);
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_NORMAL]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findAdminByUsername ($username)
    {
        return static::findOne(['username'=>$username, 'status'=>self::STATUS_NORMAL]);
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

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_NORMAL,
        ]);
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'groupId', 'name', 'lastOnlineTime'], 'required'],
            [['groupId', 'visitId', 'lastOnlineTime','status'], 'integer'],
            [['description'], 'string'],
            [['username', 'password'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'username' => '用户名',
            'password' => '密码',
            'groupId' => '所属用户组',
            'name' => '名称',
            'description' => '介绍',
            'visitId' => 'Visit ID',
            'lastOnlineTime' => '最后在线时间',
            'status' => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getStatusLabels($id=null)
    {
        $data =  [
            self::STATUS_NORMAL => Yii::t("app", "STATUS_NORMAL"),
            self::STATUS_PAUSE => Yii::t("app", "STATUS_PAUSE"),
            self::STATUS_DELETED => Yii::t("app", "STATUS_DELETED"),
        ];

        if ($id !== null && isset($data[$id])) {
            return $data[$id];
        } else {
            return $data;
        }
    }


}
