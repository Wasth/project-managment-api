<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 06.04.2019
 * Time: 0:35
 */

namespace app\controllers;


use yii\filters\Cors;
use yii\rest\ActiveController;

class RestController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];

        return $behaviors;
    }
}