<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "books".
 *
 * @property integer $id
 * @property string $name
 * @property string $date_create
 * @property string $date_update
 * @property string $preview
 * @property string $date
 * @property integer $author_id
 *
 * @property Authors $author
 */
class Books extends \yii\db\ActiveRecord
{

    public $image;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'date', 'author_id'], 'required'],
            [['date_create', 'date_update'], 'safe'],
            [['author_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => 'png, jpg'],
            [['preview'], 'string', 'max' => 100],
            [['date'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Название',
            'date_create' => 'Дата добавления',
            'date_update' => 'Дата редактирования',
            'preview'     => 'Превью',
            'date'        => 'Дата выхода книги',
            'author_id'   => 'Автор',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Authors::className(), ['id' => 'author_id']);
    }


    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->image = UploadedFile::getInstance($this, 'image');
            if(!empty($this->image)) {
                $name = $this->image->baseName . '.' . $this->image->extension;
                $this->image->saveAs(Yii::$app->basePath.'/previews/'.$name);
                $this->preview = $name;
            }
            return true;
        } else {
            return false;
        }
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }
}
