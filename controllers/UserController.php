<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 06.04.2019
 * Time: 0:41
 */

namespace app\controllers;


use app\models\User;
use Yii;

class UserController extends RestController
{
    public $modelClass = 'app\models\User';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['index']);
        unset($actions['view']);

        return $actions;
    }

    public function actionSignin()
    {
        $user = new User();

        if ($user->load(Yii::$app->request->post(), '') && $user->validate()) {
            if ($_user = User::find()->where(['email' => $user->email, 'password' => $user->password])->one()) {
                $_user->token = Yii::$app->security->generateRandomString();
                $_user->save();

                Yii::$app->response->setStatusCode(200, 'Successful signin');
                return [
                    'status' => true,
                    'token' => $_user->token,
                ];
            }

            Yii::$app->response->setStatusCode(401, 'Invalid authorization data');
            return [
                'status' => false,
                'message' => 'Invalid authorization data',
            ];
        }

        Yii::$app->response->setStatusCode(400, 'Validation Error');
        return [
            'status' => false,
            'errors' => $user->firstErrors,
        ];
    }

    public function actionWorkers() {
        if(Yii::$app->user->identity->role == 'manager'){
            Yii::$app->response->setStatusCode(200, 'Workers list');
            return [
                'status' => true,
                'workers' => User::find()->where(['role' => 'workers'])->all(),
            ];
        }

        Yii::$app->response->setStatusCode(403, 'Permission denied');
        return [
            'status' => false,
            'message' => 'Permission denied',
        ];
    }
}