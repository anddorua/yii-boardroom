<?php

namespace app\controllers;

use app\models\AppointmentChain;
use app\models\BookingForm;
use Yii;
use app\models\Appointment;
use app\models\Room;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */
class AppointmentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'book' => ['POST', 'GET'],
                    'modify' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['book', 'modify'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['book', 'modify'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionBook()
    {
        /* @var \app\models\Employee $user */
        $user = Yii::$app->user->identity;
        $booking = new BookingForm(['timeFormat' => Yii::$app->user->isGuest ? Yii::$app->params['defaultHourMode'] : $user->hour_mode]);

        if ($booking->load(Yii::$app->request->post()) && $booking->validate()) {
            //todo create appointment
            $chain = AppointmentChain::make($booking, $user->id, Yii::$app->currentRoom->id);
            $crossings = $chain->getCrossingAppointments();
            // test for crossing appointments
            if (count($crossings) > 0) {
                return $this->render('book', [
                    'model' => $booking,
                    'room' => Room::findOne(Yii::$app->currentRoom->id),
                    'hourMode' => $booking->timeFormat,
                    'firstDay' => Yii::$app->user->isGuest ? Yii::$app->params['defaultFirstDay'] : $user->first_day,
                    'crossings' => $crossings,
                ]);
            } else {
                $chain->setChainId(Appointment::getMaxChainId() + 1);
                foreach($chain as $appointment) {
                    $appointment->save();
                }
                //Yii::trace("!!writing flash successfulBooking:" . print_r($booking->attributes, true));
                Yii::$app->session->setFlash('successfulBooking', $booking->attributes);
                return $this->redirect(['site/index']);
            }
        } else {
            return $this->render('book', [
                'model' => $booking,
                'room' => Room::findOne(Yii::$app->currentRoom->id),
                'hourMode' => $booking->timeFormat,
                'firstDay' => Yii::$app->user->isGuest ? Yii::$app->params['defaultFirstDay'] : $user->first_day,
            ]);
        }
    }

    public function actionModify()
    {
        /* @var \app\models\Employee $user */
        $user = Yii::$app->user->identity;
        $booking = new BookingForm([
            'timeFormat' => Yii::$app->user->isGuest ? Yii::$app->params['defaultHourMode'] : $user->hour_mode,
            'scenario' => BookingForm::SCENARIO_MODIFY,
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!$booking->load(Yii::$app->request->post())) {
            throw new \yii\web\ServerErrorHttpException('Can`t load input data.');
        };
        /* @var \app\models\Appointment $original */
        $original = Appointment::findOne($booking->appId);
        $booking->date = $original->getTimeStart()->format('Y-m-d');
        if (count($errors = ActiveForm::validate($booking)) == 0) {
            $chain = AppointmentChain::loadChain($original->chain);
            $chain->applyFilter(new \DateTime());
            if ($booking->applyToAll == 1) {
                $chain->applyChange($booking);
            } else {
                $chain->applyChangeToMember($original->id, $booking);
            }

            // test for crossing appointments
            $crossings = $chain->getCrossingAppointments();
            if (count($crossings) > 0) {
                $errors[Html::getInputId($booking, 'global')] = [$this->getCrossingError($crossings, $booking->timeFormat)];
                return $errors;
            } else {
                $chain->setChainId(Appointment::getMaxChainId() + 1);
                $chain->saveChain();
                Yii::$app->session->setFlash('successfulBooking', $booking->attributes);
                return $errors;
            }
        } else {
            return $errors;
        }
    }

    /**
     * @param $crossings array of \app\models\Appointment
     * @param $timeFormat integer
     * @return string
    */
    private function getCrossingError($crossings, $timeFormat)
    {
        return $this->renderPartial('_crossingsError', ['crossings' => $crossings, 'hourMode' => $timeFormat]);
    }

    public function actionBookInfo($appId)
    {
        /* @var $app Appointment */
        $app = Appointment::findOne($appId);
        $booking = new BookingForm([
            'timeFormat' => Yii::$app->user->isGuest ? Yii::$app->params['defaultHourMode'] : Yii::$app->user->identity->hour_mode,
        ]);
        $booking->fillFromAppointment($app);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $booking;
    }

}
