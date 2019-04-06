<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.04.2019
 * Time: 12:35
 */

namespace app\controllers;


use app\filters\MyBearerAuth;
use app\models\Comment;
use app\models\Task;
use Yii;

class CommentController extends RestController
{
    public $modelClass = 'app\models\Comment';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['bearerAuth'] = [
            'class' => MyBearerAuth::className(),
        ];

        return $behaviors;
    }

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

    public function actionCreate($task_id){
        if($task = Task::findOne($task_id)) {
            if($task->status == 'new' || $task->status == 'in-work'){
                $comment = new Comment();
                if($comment->load(Yii::$app->request->post(), '') && $comment->validate()){
                    $comment->task_id = $task_id;
                    $comment->user_id = Yii::$app->user->id;
                    $comment->save();
                    Yii::$app->response->setStatusCode(201, 'Successful creation');
                    return [
                        'status' => true,
                        'message' => 'Successful creation',
                    ];

                }

                Yii::$app->response->setStatusCode(400, 'Validation Error');
                return [
                    'status' => false,
                    'message' => 'Validation Error',
                ];
            }

            Yii::$app->response->setStatusCode(404, 'Task is already closed');
            return [
                'status' => false,
                'message' => 'Task is already closed',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Task not found');
        return [
            'status' => false,
            'message' => 'Task not found',
        ];
    }

}