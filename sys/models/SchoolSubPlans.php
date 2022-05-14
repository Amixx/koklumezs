<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SchoolSubPlans extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolsubplans';
    }

    public function rules()
    {
        return [
            [['school_id', 'name', 'months', 'max_pause_weeks'], 'required'],
            [['school_id', 'months', 'max_pause_weeks', 'pvn_percent', 'days_for_payment'], 'number'],
            [['name', 'description', 'files', 'message', 'type', 'stripe_single_price_id', 'stripe_recurring_price_id'], 'string'],
            [['recommend_after_trial', 'allow_single_payment'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'type' => \Yii::t('app',  'Tips'),
            'pvn_percent' => \Yii::t('app',  'PVN (percentage)'),
            'months' => \Yii::t('app',  'Months (0 - unlimited)'),
            'max_pause_weeks' => \Yii::t('app',  'Pause weeks'),
            'files' => \Yii::t('app',  'Files'),
            'message' => \Yii::t('app',  'Message to send with the invoice'),
            'days_for_payment' => \Yii::t('app', 'How many days to pay the bill'),
            'recommend_after_trial' => \Yii::t('app', 'Should this plan be recommended to students after trial expiration'),
            'allow_single_payment' => \Yii::t('app', 'Allow to pay for the entire plan in one installment'),
            'stripe_single_price_id' => \Yii::t('app', 'Stripe price ID (for single payment)'),
            'stripe_recurring_price_id' => \Yii::t('app', 'Stripe price ID (for monthly payment)'),
        ];
    }

    public function typeText()
    {
        return $this->type === 'lesson' ? \Yii::t('app', 'subscription') : \Yii::t('app', 'rent');
    }

    public function price()
    {
        return SchoolSubplanParts::getPlanTotalCost($this->id);
    }

    public static function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public static function getForCurrentSchool()
    {
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        return self::getForSchool($schoolId);
    }

    public static function getMappedForSelection()
    {
        return ArrayHelper::map(self::getForCurrentSchool()->asArray()->all(), 'id', 'name');
    }

    public static function getPrices()
    {
        $isAdmin = Yii::$app->user->identity->user_level == 'Admin';
        $query = $isAdmin ? self::find() : self::getForCurrentSchool();
        $data = $query->asArray()->all();

        $res = [];

        foreach ($data as $item) {
            $price = SchoolSubplanParts::getPlanTotalCost($item['id']);
            $res[] = $price;
        }

        return $res;
    }

    public static function getRecommendedPlansAfterTrial()
    {
        $plans = self::getForCurrentSchool()->andWhere(['recommend_after_trial' => 1])->all();
        $planPrices = [];
        foreach ($plans as $plan) {
            $planPrices[$plan['id']] = self::getSubPlanStripePrices($plan);
        }

        return [
            'plans' => $plans,
            'planPrices' => $planPrices,
        ];
    }

    public static function getSubPlanStripePrices($plan)
    {
        $prices = [
            'single' => null,
            'recurring' => null,
        ];

        $stripe = new \Stripe\StripeClient(
            'sk_test_51KHnfwH3bdDtJYNRBaeTBL8XB6X6w4hggXIXHONhVdYVxbuwYYBHC1qmmqLKueJ9mzsVqs5aj21K0hO5fLUzr9dS00L9ZT33Jc'
        );

        if ($plan['stripe_single_price_id']) {
            $stripePrice = self::fetchPrice($stripe, $plan['stripe_single_price_id']);
            $prices['single'] = number_format($stripePrice['unit_amount'] / 100, 2);
        }
        if ($plan['stripe_recurring_price_id']) {
            $stripePrice = self::fetchPrice($stripe, $plan['stripe_recurring_price_id']);
            $prices['recurring'] = number_format($stripePrice['unit_amount'] / 100, 2);
        }

        return $prices;
    }

    private static function fetchPrice($stripe, $priceId)
    {
        return $stripe->prices->retrieve(
            $priceId,
            []
        );
    }
}
