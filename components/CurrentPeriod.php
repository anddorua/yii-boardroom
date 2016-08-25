<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13.06.16
 * Time: 22:14
 */

namespace app\components;


use yii\base\Component;

/**
 * Class CurrentPeriod
 * @package app\components
 * @property \DateTime current
 */
class CurrentPeriod extends Component
{
    const SESSION_CURRENT = 'current';

    /**
     * @return \DateTime
     */
    public function getCurrent()
    {
        if (!\Yii::$app->session->has(self::SESSION_CURRENT)) {
            $this->storeCurrent(new \DateTime());
        }
        return new \DateTime(\Yii::$app->session->get(self::SESSION_CURRENT));
    }

    private function storeCurrent(\DateTime $value)
    {
        \Yii::$app->session->set(self::SESSION_CURRENT, $value->format(DATE_ATOM));
    }

    /**
     * @return \DateTime
     */
    public function next()
    {
        $current = $this->getCurrent();
        $current->add(new \DateInterval('P1M'));
        $this->storeCurrent($current);
        return $current;
    }

    /**
     * @return \DateTime
     */
    public function prev()
    {
        $current = $this->getCurrent();
        $current->sub(new \DateInterval('P1M'));
        $this->storeCurrent($current);
        return $current;
    }
}