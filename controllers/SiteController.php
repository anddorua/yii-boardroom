<?php

namespace app\controllers;

use app\models\Appointment;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\DisplayPeriod;
use app\models\Room;
use app\models\Employee;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        /* @var $employee \app\models\Employee
         * @var Yii::$app->currentRoom \app\components\CurrentRoom
         */
        $employee = Yii::$app->user->identity;
        $firstDay = is_object($employee) ? $employee->first_day : Yii::$app->params['defaultFirstDay'];
        $hourMode = is_object($employee) ? $employee->hour_mode : Yii::$app->params['defaultHourMode'];

        $successfulBooking = Yii::$app->session->getFlash('successfulBooking');
        //Yii::trace("!!successfulBooking is " . print_r($successfulBooking, true));
        return $this->render('index', [
            'tpl_browse_first_day' => $firstDay,
            'tpl_browse_calendar' => Appointment::getMonthAppointments(Yii::$app->currentRoom->id, Yii::$app->currentPeriod->getCurrent()),
            'tpl_browse_hour_mode' => $hourMode,
            'room' => Room::findOne(Yii::$app->currentRoom->id),
            'successfulBooking' => $successfulBooking,
        ]);
    }

    public function actionPeriod($month)
    {
        switch ($month) {
            case 'prev':
                Yii::$app->currentPeriod->prev();
                break;
            case 'next':
                Yii::$app->currentPeriod->next();
                break;
        }
        return $this->redirect(['site/index']);
    }

    public function actionRoom($room)
    {
        Yii::$app->currentRoom->id = $room;
        return $this->redirect(['site/index']);
    }

    public function actionLogin()
    {
        //Yii::trace("Action login invoked");
        if (!Yii::$app->user->isGuest) {
            //Yii::trace("!!Seems we are not guest, going home.");
            return $this->goHome();
        }

        $model = new LoginForm();
        //Yii::trace("!!Login form model created");
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //Yii::trace("!!Login procedure passed, going back");
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
