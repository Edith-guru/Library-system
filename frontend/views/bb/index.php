<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use frontend\models\Book;
use frontend\models\BorrowedBook;
use frontend\models\Student;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\BorrowedBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MASOMO LMS';
$this->params['breadcrumbs'][] = $this->title;

$totalBooks = Book::find()->asArray()->all();
if(\Yii::$app->user->can('student')){
    $studentId = Student::find()->where(['userId'=>\yii::$app->user->id])->one();
    $bb = BorrowedBook::find()->Where(['studentId'=>$studentId->studentsId])->andWhere(['actualReturnDate'=>NULL])->asArray()->all();
}
if(\Yii::$app->user->can('librarian')){
    $bb = BorrowedBook::find()->andWhere(['actualReturnDate'=>NULL])->asArray()->all();
}
if(\Yii::$app->user->can('admin')){
  $bb = BorrowedBook::find()->andWhere(['actualReturnDate'=>NULL])->asArray()->all();
}
$totalStudents = Student::find()->asArray()->all();
$overdue = BorrowedBook::find()->where('expectedReturn > '.date('yy/m/d'))->andWhere(['actualReturnDate'=>NULL])->asArray()->all();

?>
<div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-book"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">TOTAL BOOKS</span>
              <span class="info-box-number"><?= count($totalBooks)?><small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-shopping-cart"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">BORROWED BOOKS</span>
              <span class="info-box-number"><?= count($bb)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-stack-overflow"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">OVERDUE BOOKS</span>
              <span class="info-box-number"><?= count($overdue)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">TOTAL STUDENTS</span>
              <span class="info-box-number"><?= count($totalStudents) ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>


      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
        	<div style="padding-top: 20px;">
        	<?php if(\Yii::$app->user->can('student')){?>
        	      <button val="create" type="button" class="btn btn-block btn-success btn-lg borrowbook" style="width: 300px;"><i class="fa fa-plus" aria-hidden="true"></i> Borrow a Book</button>
        	<?php }?>
        	<?php if(\Yii::$app->user->can('librarian')){?>
        	     <button type="button" class="btn btn-block btn-success btn-lg assighnbook" style="width: 300px;"><i class="fa fa-plus" aria-hidden="true"></i> Assighn a Book</button>
        	<?php }?>
            </div>
            <div style="text-align: center;">
                 <h2 class="box-title"><strong>BOOK ASSIGNMENTS</strong></h2>
            </div>
              <div class="box-tools">
                <div class="input-group input-group-sm hidden-xs" style="width: 300px;">
                  <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                      <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
            
                        //'bbId',
                        [
                            'attribute' => 'studentId',
                            'value' => function ($dataProvider) {
                                $studentName = Student::find()->where(['studentsId'=>$dataProvider->studentId])->One();
                                return $studentName->fullName;
                            },
                        ],
                        [
                            'attribute' => 'bookId',
                            'value' => function ($dataProvider) {
                            $studentName = Book::find()->where(['bookId'=>$dataProvider->bookId])->One();
                            return $studentName->bookName;
                            },
                        ],
                        [
                            'attribute' => 'borrowDate',
                            'value' => function ($dataProvider) {
                                $date = new DateTime($dataProvider->borrowDate);
                                return $date->format('F j, Y,');
                            },
                        ],
                        [
                            'attribute' => 'expectedReturn',
                            'value' => function ($dataProvider) {
                            $date = new DateTime($dataProvider->expectedReturn);
                            return $date->format('F j, Y,');
                            },
                        ],
                        'actualReturnDate',
                        [
                            'label'=>'Return Book',
                            'format' => 'raw',
                            'value' => function ($dataProvider) {
                            if(\Yii::$app->user->can('librarian')){
                                return '<span val="'.$dataProvider->bbId.'" class="btn btn-danger returnbook">Return</span>';
                                }
                            return '';
                            },
                            
                        ],
                        [
                            'label'=>'Status',
                            'format' => 'raw',
                            'value' => function ($dataProvider) {
                                $bookStatus = Book::find()->where(['bookId'=>$dataProvider->bookId])->One();
                                if($bookStatus->status == 0){
                                    $button = 'btn btn-info';
                                    $status = 'Available';
                                }elseif ($bookStatus->status == 1){
                                    $button = 'btn btn-success';
                                    $status = 'Issued';
                                }elseif ($bookStatus->status == 2){
                                    $button = 'btn btn-warning';
                                    $status = 'Pending';
                                }
                                return '<span class="'.$button.'">'.$status.'</span>';
                            },
                            
                        ],
                        [
                            'label'=>'Approve Books',
                            'format' => 'raw',
                            'value' => function ($dataProvider) {
                            $bookStatus = Book::find()->where(['bookId'=>$dataProvider->bookId])->One();
                            if(\Yii::$app->user->can('librarian') && $bookStatus->status == 2){
                                return Html::a('Approve', ['approve','id'=>$dataProvider->bookId,'studentId'=>$dataProvider->studentId], ['class' => 'btn btn-success']);
                            }
                            return '';
                            },
                            
                         ],
                            
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>



	<?php
        Modal::begin([
            'header'=>'<h4>Return Book</h4>',
            'id'=>'returnbook',
            'size'=>'modal-md'
            ]);

        echo "<div id='returnbookContent'></div>";
        Modal::end();
      ?>

   <?php
        Modal::begin([
            'header'=>'<h4>Assighn A Book</h4>',
            'id'=>'assignbook',
            'size'=>'modal-lg'
            ]);

        echo "<div id='assignbookContent'></div>";
        Modal::end();
      ?>
