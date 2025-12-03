<?php

namespace frontend\controllers;

use Yii;
use common\models\Book;
use common\models\BookSearch;
use common\models\Author;
use common\models\BookAuthor;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'report'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();
        $authors = Author::find()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');
            $model->author_ids = Yii::$app->request->post('Book')['author_ids'] ?? [];
            
            if ($model->validate()) {
                if ($model->coverImageFile) {
                    $model->upload();
                }
                
                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $authors = Author::find()->all();
        $model->author_ids = $model->getAuthors()->select('id')->column();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');
            $model->author_ids = Yii::$app->request->post('Book')['author_ids'] ?? [];
            
            if ($model->validate()) {
                if ($model->coverImageFile) {
                    $model->upload();
                }
                
                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Report: Top 10 authors by books count for specific year
     * @return mixed
     */
    public function actionReport()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        
        $authors = Author::find()
            ->select([
                'author.*',
                'COUNT(book.id) as books_count'
            ])
            ->innerJoinWith('books')
            ->where(['book.year' => $year])
            ->groupBy('author.id')
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('report', [
            'authors' => $authors,
            'year' => $year,
        ]);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}