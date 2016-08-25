<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 09.05.16
 * Time: 15:04
 */

namespace app\components;


use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use app\models\Employee;
use \yii\helpers\Url;
use Yii;

class EmptyPasswordActionRedirector extends ActionFilter
{
    public $emptyPasswordRoute;
    public $allowed;

    public function init()
    {
        parent::init();
        $this->allowed = (array)$this->allowed;
        for($i = 0; $i < count($this->allowed); $i++) {
            $this->allowed[$i] = trim($this->allowed[$i], '/');
        }
    }

    public function beforeAction($action)
    {
        Yii::trace("!!Redirector action filter works.");
        if (!Yii::$app->user->isGuest) { //User not guest.
            /* @var $employee Employee */
            $employee = Yii::$app->user->identity;
            $emptyPassword = $employee->isEmptyPassword();
            if ($emptyPassword && !$this->isRouteAllowed(trim(Yii::$app->requestedRoute, '/'))) {
                return $this->redirectToProfileUpdate($employee);
            }
        }
        return true;
    }

    private function redirectToProfileUpdate(Employee $employee)
    {
        if ($this->emptyPasswordRoute !== null) {
            $passwordUrl = (array) $this->emptyPasswordRoute;
            if (trim($passwordUrl[0],'/') !== trim(Yii::$app->requestedRoute, '/')) {
                Yii::$app->getResponse()->redirect(Url::to([$this->emptyPasswordRoute, 'id' => $employee->id]));
                return false;
            } else {
                return true;
            }
        }
        throw new ForbiddenHttpException(Yii::t('Application', 'You should set non blank password.'));
    }

    private function isRouteAllowed($route) {
        return in_array($route, $this->allowed);
    }
}