<?php

namespace app\controllers;

use Yii;
use app\models\Employee;
use app\models\EmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PasswordChange;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
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
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee(['scenario' => Employee::SCENARIO_NEW_FROM_SCHEMA]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $editOwnProfile = $id == Yii::$app->user->id;

        $passChange = new PasswordChange();
        $modelsToShow = ['model' => $model];
        if ($editOwnProfile) {
            $passChange->scenario = is_null($model->pwd_hash) ? PasswordChange::SCENARIO_NO_PASSWORD : PasswordChange::SCENARIO_HAS_PASSWORD;
            $modelsToShow['passChange'] = $passChange;
        } else {
            $passChange->scenario = PasswordChange::SCENARIO_ALIEN_PROFILE;
        }
        Yii::trace("!! scenario is " . $passChange->scenario);

        $modelTest = $model->load(Yii::$app->request->post()) && $model->validate();
        $passChangeTest = !$editOwnProfile || (
            $passChange->load(Yii::$app->request->post())
            && $passChange->setEmployee($model)
            && $passChange->validate());

        if ($modelTest && $passChangeTest) {
            if (!empty($passChange->newPassword1)) {
                $model->setPassword($passChange->newPassword1);
            }
            if ($model->save(false)) {
                return $this->redirect(['index', 'id' => $model->id]);
            }
            return $this->render('update', $modelsToShow);
        } else {
            return $this->render('update', $modelsToShow);
        }
    }

    /**
     * Deletes an existing Employee model.
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
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
