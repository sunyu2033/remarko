<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sys_admin_group".
 *
 * @property string $id
 * @property string $name
 * @property string $catalogs
 * @property string $description
 */
class AdminGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_group}}';
    }

    public function getAdmin()
    {
        return $this->hasMany(Admin::className(), ['groupId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['catalogs', 'description'], 'string'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'catalogs' => 'Catalogs',
            'description' => 'Description',
        ];
    }

    public static function getAdminGroups($id=null) {
        $groups = [];
        $adminGroups = AdminGroup::find()->all();
        foreach($adminGroups as $value) {
            $groups[$value->id] = $value->name;
        }
        if ($id !== null && isset($groups[$id])) {
            return $groups[$id];
        } else {
            return $groups;
        }
    }
}
