<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 06.04.2019
 * Time: 1:03
 */

namespace app\controllers;


use app\filters\MyBearerAuth;
use app\models\Project;
use Yii;

class ProjectController extends RestController
{
    public $modelClass = 'app\models\Project';

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

    public function actionCreate()
    {
        if (Yii::$app->user->identity->role == 'manager') {
            $project = new Project();
            if ($project->load(Yii::$app->request->post(), '') && $project->validate()) {
                $project->save(false);

                Yii::$app->response->setStatusCode(201, 'Successful creation');
                return [
                    'status' => true,
                    'project' => $project,
                ];
            }

            Yii::$app->response->setStatusCode(400, 'Validation Error');
            return [
                'status' => false,
                'errors' => $project->firstErrors,
            ];
        }

        Yii::$app->response->setStatusCode(403, 'Permission denied');
        return [
            'status' => false,
            'message' => 'Permission denied',
        ];
    }

    public function actionDelete($id)
    {
        if($project = Project::findOne($id)) {
            if($project->manager_id != Yii::$app->user->id){
                $project->delete();

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

        Yii::$app->response->setStatusCode(404, 'Project not found');
        return [
            'status' => false,
            'message' => 'Project not found',
        ];
    }
}