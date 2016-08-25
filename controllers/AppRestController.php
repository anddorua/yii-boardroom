<?php

namespace app\controllers;

use app\models\Appointment;
use app\models\AppointmentChain;
use app\models\Employee;
use yii\rest\ActiveController;
use Yii;
use yii\web\Response;

class AppRestController extends ActiveController
{
    public $modelClass = 'app\models\Appointment';

    public function actionChainLength($chainId)
    {
        $chain = AppointmentChain::loadChain($chainId);
        $chain->applyFilter(new \DateTime());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['count' => $chain->count()];
    }

    public function actionAllowedOptions($appId)
    {
        /* @var $appointment Appointment */
        $appointment = Appointment::findOne($appId);
        $chain = AppointmentChain::loadChain($appointment->chain);
        $chain->applyFilter(new \DateTime());
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'applyToAll' => $chain->count() > 1,
            'editable' => false, // if not guest and user created this appointment or user is admin
            'changeEmployee' => false, // if user is admin
        ];
        if (!Yii::$app->user->isGuest) {
            /* @var $user Employee */
            $user = Yii::$app->user->identity;
            $result['editable'] = $appointment->emp_id == $user->id || $appointment->creator_id == $user->id || $user->is_admin == 1;
            $result['changeEmployee'] = $user->is_admin == 1 || $appointment->creator_id == $user->id;
        }
        return $result;
    }
}
