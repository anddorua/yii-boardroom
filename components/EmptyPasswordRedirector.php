<?php
/**
 * Created by PhpStorm.
 * User: aahom_000
 * Date: 08.05.2016
 * Time: 17:05
 */

namespace app\components;

use \yii\base\Behavior;
use \yii\base\Action;
use \yii\base\Application;
use Yii;

class EmptyPasswordRedirector extends Behavior
{
    public $emptyPasswordRoute;
    /**
     * Declares event handlers for the [[owner]]'s events.
     *
     * Child classes may override this method to declare what PHP callbacks should
     * be attached to the events of the [[owner]] component.
     *
     * The callbacks will be attached to the [[owner]]'s events when the behavior is
     * attached to the owner; and they will be detached from the events when
     * the behavior is detached from the component.
     *
     * The callbacks can be any of the following:
     *
     * - method in this behavior: `'handleClick'`, equivalent to `[$this, 'handleClick']`
     * - object method: `[$object, 'handleClick']`
     * - static method: `['Page', 'handleClick']`
     * - anonymous function: `function ($event) { ... }`
     *
     * The following is an example:
     *
     * ```php
     * [
     *     Model::EVENT_BEFORE_VALIDATE => 'myBeforeValidate',
     *     Model::EVENT_AFTER_VALIDATE => 'myAfterValidate',
     * ]
     * ```
     *
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest',
        ];
    }

    public function beforeRequest($event)
    {
        Yii::trace("!!My redirector!!");
        list ($route, $params) = Yii::$app->request->resolve();
        Yii::trace("!!current route is: " . $route);
        Yii::trace("!!emptyPasswordRoute is: " . $this->emptyPasswordRoute);
        if (Yii::$app->user->isGuest) {
            Yii::trace("!!user is guest");
        } else {
            /**
             * @var $employee \app\models\Employee
             */
            $employee = Yii::$app->user->identity;
            $emptyPassword = $employee->isEmptyPassword();
            Yii::trace("!!user`s password is " . ($emptyPassword ? 'empty' : 'filled'));
            if ($emptyPassword) {
                //Yii::$app->getResponse()->redirect([$this->emptyPasswordRoute, 'id' => $employee->id]);
                Yii::trace("!!redirect to " . \yii\helpers\Url::to([$this->emptyPasswordRoute, 'id' => $employee->id]));
            }
        }
    }


}