<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\BoardroomAsset;

AppAsset::register($this);
BoardroomAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $navItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    $rooms = \app\models\Room::find()->orderBy(['room_name' => SORT_ASC])->all();
    $roomItems = [];
    foreach($rooms as $room) {
        $item = [
            'label' => $room->room_name,
            'url' => ['/site/room', 'room' => $room->id],
        ];
        if ($room->id == Yii::$app->currentRoom->id) {
            $item['options'] = ['class' => ['active']];
        }
        $roomItems[] = $item;
    }
    $navItems[] = [
        'label' => 'Boardroom',
        'items' => $roomItems,
    ];
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->is_admin) {
            $navItems[] = ['label' => 'Users', 'url' => ['/employee/index']];
        }
        $navItems[] = ['label' => 'Profile', 'url' => ['/employee/update', 'id' => Yii::$app->user->id]];
    }
    $navItems[] =
        Yii::$app->user->isGuest ? (
        ['label' => 'Login', 'url' => ['/site/login']]
        ) : (
            '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->login . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>'
        );
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $navItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
