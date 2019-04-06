<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.04.2019
 * Time: 9:56
 */

namespace app\controllers;


use app\filters\MyBearerAuth;
use app\models\Project;
use app\models\Task;
use app\models\User;
use Yii;

class TaskController extends RestController
{
    public $modelClass = 'app\models\Task';

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

    public function actionCreate($project_id)
    {
        if ($project = Project::findOne($project_id)) {
            if ($project->manager_id == Yii::$app->user->id) {

                $task = new Task();

                if ($task->load(Yii::$app->request->post(), '') && $task->validate()) {
                    $task->project_id = $project_id;
                    $task->status = 'new';
                    $task->save(false);
                    Yii::$app->response->setStatusCode(200, 'Successful creation');
                    return [
                        'status' => true,
                        'message' => 'Successful creation',
                    ];
                }

                Yii::$app->response->setStatusCode(400, 'Validation error');
                return [
                    'status' => false,
                    'message' => 'Validation error',
                ];
            }
            Yii::$app->response->setStatusCode(403, 'Permission denied');
            return [
                'status' => false,
                'message' => 'Permission denied',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Project not found');
        return [
            'status' => false,
            'message' => 'Project not found',
        ];
    }


    public function actionSetWorker($task_id, $worker_id)
    {
        if ($task = Task::findOne($task_id)) {
            if ($task->project->manager_id == Yii::$app->user->id) {
                if ($task->status == 'new' || $task->worker_id) {
                    if ($user = User::find()->where(['id' => $worker_id, 'role' => 'worker'])) {
                        $task->worker_id = $worker_id;
                        $task->status = 'in-work';
                        Yii::$app->response->setStatusCode(200, 'Worker are attached');
                        return [
                            'status' => true,
                            'message' => 'Worker are attached',
                        ];
                    }
                    Yii::$app->response->setStatusCode(404, 'Worker not found');
                    return [
                        'status' => false,
                        'message' => 'Worker not found',
                    ];
                }

                Yii::$app->response->setStatusCode(400, 'Task already taken');
                return [
                    'status' => false,
                    'message' => 'Task already taken',
                ];
            }
            Yii::$app->response->setStatusCode(403, 'Permission denied');
            return [
                'status' => false,
                'message' => 'Permission denied',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Task not found');
        return [
            'status' => false,
            'message' => 'Task not found',
        ];
    }

    public function actionTest($task_id)
    {
        return $task_id;
    }

    public function actionSetModeration($task_id)
    {
        if ($task = Task::findOne($task_id)) {
            if ($task->worker_id == Yii::$app->user->id) {
                if ($task->status == 'in-work') {
                    $task->status = 'moderation';
                    $task->save(false);
                    Yii::$app->response->setStatusCode(200, 'Task is in moderation');
                    return [
                        'status' => true,
                        'message' => 'Task is in moderation',
                    ];
                }
                Yii::$app->response->setStatusCode(400, 'Task is not in-work');
                return [
                    'status' => false,
                    'message' => 'Task is not in-work',
                ];
            }
            Yii::$app->response->setStatusCode(403, 'Permission denied');
            return [
                'status' => false,
                'message' => 'Permission denied',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Task not found');
        return [
            'status' => false,
            'message' => 'Task not found',
        ];
    }

    public function actionSetDone($task_id)
    {
        if ($task = Task::findOne($task_id)) {
            if ($task->project->manager_id == Yii::$app->user->id) {
                if ($task->status == 'moderation') {
                    $task->status = 'done';
                    $task->save();
                    Yii::$app->response->setStatusCode(200, 'Task done');
                    return [
                        'status' => true,
                        'message' => 'Task done',
                    ];
                }
                Yii::$app->response->setStatusCode(400, 'Task is not in moderation');
                return [
                    'status' => false,
                    'message' => 'Task is not in moderation',
                ];
            }
            Yii::$app->response->setStatusCode(403, 'Permission denied');
            return [
                'status' => false,
                'message' => 'Permission denied',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Task not found');
        return [
            'status' => false,
            'message' => 'Task not found',
        ];
    }

    public function actionDelete($id)
    {
        if ($task = Task::findOne($id)) {
            if ($task->project->manager_id == Yii::$app->user->id) {
                $task->delete();

                Yii::$app->response->setStatusCode(204, 'Successful delete');
                return [
                    'status' => true,
                    'message' => 'Successful delete',
                ];
            }
            Yii::$app->response->setStatusCode(403, 'Permission denied');
            return [
                'status' => false,
                'message' => 'Permission denied',
            ];
        }

        Yii::$app->response->setStatusCode(404, 'Task not found');
        return [
            'status' => false,
            'message' => 'Task not found',
        ];
    }
}