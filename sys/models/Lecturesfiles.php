<?php

namespace app\models;


/**
 * This is the model class for table "lecturesfiles".
 *
 * @property int $id
 * @property string $file Fails
 * @property string $thumb Bilde
 * @property string $title Virsraksts
 * @property int $lecture_id Lekcija
 *
 * @property Lectures $lecture
 */
class Lecturesfiles extends \yii\db\ActiveRecord
{
    const FILE_EXTENSIONS = [
        'video' => ['mp4', 'mov', 'ogv', 'webm', 'flv', 'avi', 'f4v'],
        'docs' => ['doc', 'docx', 'pdf', 'txt'],
        'audio' => ['aac', 'alac', 'amr', 'flac', 'mp3', 'opus', 'vorbis', 'ogg', 'wav'],
    ];

    public static function tableName()
    {
        return 'lecturesfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file', 'lecture_id'], 'required'],
            [['file', 'title', 'thumb'], 'string'],
            [['lecture_id'], 'integer'],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => \Yii::t('app',  'Title'),
            'file' => \Yii::t('app',  'File'),
            'thumb' => \Yii::t('app',  'Image'),
            'lecture_id' => \Yii::t('app',  'Lesson'),
        ];
    }

    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    public static function getLectureFiles($id)
    {
        $fileGroups = [
            'video' => [],
            'docs' => [],
            'audio' => [],
        ];

        $lecturefiles = self::find()->where(['lecture_id' => $id])->asArray()->all();

        foreach ($lecturefiles as $id => $file) {
            if (!isset($file['file']) || empty($file['file'])) {
                continue;
            }

            if (strpos($file['file'], "youtube") !== false) {
                $fileGroups['video'][] = $file;
                continue;
            }

            $path_info = pathinfo($file['file']);
            if (!isset($path_info['extension'])) {
                continue;
            }

            foreach (self::FILE_EXTENSIONS as $type => $fileExtensions) {
                if (in_array(strtolower($path_info['extension']), $fileExtensions)) {
                    $fileGroups[$type][] = $file;
                    break;
                }
            }
        }

        return $fileGroups;
    }
}
