<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 06.04.2019
 * Time: 1:09
 */
namespace app\filters;

use Yii;

class MyBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    public function handleFailure($response)
    {
        Yii::$app->response->setStatusCode(403, 'Permission denied');
        Yii::$app->response->content = json_encode([
            'status' => false,
            'message' => 'Permission denied'
        ]);
    }
}