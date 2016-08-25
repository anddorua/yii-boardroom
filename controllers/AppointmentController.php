<?php

namespace app\controllers;

use app\models\AppointmentChain;
use app\models\BookingForm;
use Yii;
use app\models\Appointment;
use app\models\AppointmentSearch;
use app\models\Room;
use yii\base\Response;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                    'delete' => ['POST'],
                    'book' => ['POST'],
                    'modify' => ['POST'],

                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['book'],
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

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Appointment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Appointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Appointment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
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

    /**
     * Updates an existing Appointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Appointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Appointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appointment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
