<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */
/* @var $passChange app\models\PasswordChange */

$this->title = 'Update Employee: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        $modelsToShow = ['model' => $model];
        if (isset($passChange)) {
            $modelsToShow['passChange'] = $passChange;
        }
    ?>
    <?= $this->render('_form', $modelsToShow) ?>

</div>
