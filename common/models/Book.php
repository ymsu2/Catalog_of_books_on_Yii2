<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property string|null $cover_image
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property BookAuthor[] $bookAuthors
 */
class Book extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $coverImageFile;
    
    /**
     * @var array
     */
    public $author_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'year', 'isbn'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => date('Y')],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['cover_image'], 'string', 'max' => 255],
            [['coverImageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['author_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год издания',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image' => 'Обложка',
            'coverImageFile' => 'Файл обложки',
            'author_ids' => 'Авторы',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * @return string|null
     */
    public function getAuthorsString()
    {
        $authors = [];
        foreach ($this->authors as $author) {
            $authors[] = $author->name;
        }
        return implode(', ', $authors);
    }

    /**
     * @return string|null
     */
    public function getCoverImageUrl()
    {
        if ($this->cover_image) {
            return Yii::$app->request->baseUrl . '/uploads/covers/' . $this->cover_image;
        }
        return null;
    }

    /**
     * Upload cover image
     * @return bool
     */
    public function upload()
    {
        if ($this->coverImageFile) {
            $directory = Yii::getAlias('@frontend/web/uploads/covers');
            if (!file_exists($directory)) {
                FileHelper::createDirectory($directory);
            }
            
            $fileName = uniqid() . '.' . $this->coverImageFile->extension;
            $filePath = $directory . '/' . $fileName;
            
            if ($this->coverImageFile->saveAs($filePath)) {
                // Delete old image if exists
                if ($this->cover_image) {
                    $oldFilePath = $directory . '/' . $this->cover_image;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                $this->cover_image = $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Delete cover image
        if ($this->cover_image) {
            $filePath = Yii::getAlias('@frontend/web/uploads/covers/') . $this->cover_image;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        // Заполняем author_ids при загрузке модели
        $this->author_ids = $this->getAuthors()->select('id')->column();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Сохраняем связи с авторами
        if (is_array($this->author_ids)) {
            // Удаляем старые связи
            BookAuthor::deleteAll(['book_id' => $this->id]);
            
            // Добавляем новые связи
            foreach ($this->author_ids as $authorId) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $this->id;
                $bookAuthor->author_id = $authorId;
                $bookAuthor->save();
            }
        }
    }
}