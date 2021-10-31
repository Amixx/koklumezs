<?php

namespace app\models;

use Yii;
use yii\base\Model;

class JoinSchoolForm extends Model
{
    private $validDomains = [
        "koklumezs",
        "stigupasaule",
        "ritmapasaule",
        "127.0.0.1:8000" // lai lokāli varētu notestēt
    ];
    public $linkToSchool;

    public function rules()
    {
        return [
            [['linkToSchool'], 'required'],
            [['linkToSchool'], 'string'],
            ['linkToSchool', 'validateLink'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'linkToSchool' => \Yii::t('app', 'A link to join the school'),
        ];
    }

    public function validateLink($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!filter_var($this->linkToSchool, FILTER_VALIDATE_URL)) {
                $this->addError($attribute, Yii::t('app', 'Invalid link!'));
            }

            $urlContainsValidDomain = false;
            foreach ($this->validDomains as $domain) {
                if (strpos($this->linkToSchool, $domain)) {
                    $urlContainsValidDomain = true;
                }
            }
            $urlContainsRequiredParams = strpos($this->linkToSchool, "?s=") && strpos($this->linkToSchool, "&l=");

            if (!$urlContainsValidDomain || !$urlContainsRequiredParams) {
                $this->addError($attribute, Yii::t('app', 'Invalid link!'));
            }
        }
    }

    public function addUserToNewSchool()
    {
        $schoolStudent = new SchoolStudent;
        $schoolStudent->school_id = $this->getSchoolIdFromLink();
        $schoolStudent->user_id = Yii::$app->user->identity->id;
        $schoolStudent->signed_up_to_rent_instrument = false;
        $schoolStudent->has_instrument = true;
        $schoolStudent->show_real_lessons = true;
        // jāved uz jautājumiem un tad tikai jātaisa profils

        return $schoolStudent->save();
    }

    private function getSchoolIdFromLink()
    {
        $parsedUrl = parse_url($this->linkToSchool);
        parse_str($parsedUrl['query'], $params);

        return (int) $params['s'];
    }
}
