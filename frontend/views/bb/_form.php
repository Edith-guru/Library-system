<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\Student;
use frontend\models\Book;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model frontend\models\BorrowedBook */
/* @var $form yii\widgets\ActiveForm */

$sudents = ArrayHelper::map(Student::find()->all(), 'studentsId', 'fullName');
$books = ArrayHelper::map(Book::find()->where(['status'=>0])->all(), 'bookId', 'bookName');
?>

<div class="borrowed-book-form">

    <?php $form = ActiveForm::begin(['id' => 'bb-create']); ?>
    
     <?= $form->field($model, 'borrowDate')->hiddenInput(['value'=>date('yy/m/d')])->label(false) ?>
    
    <?php if($borrow==1){
        $studentId = Student::find()->where(['userId'=>\yii::$app->user->id])->one();
     ?>
        <?= $form->field($model, 'studentId')->hiddenInput(['value'=>$studentId->studentsId])->label(false) ?>
    <?php }else{?>
    	<?= $form->field($model, 'studentId')->dropDownList($sudents) ?>
	<?php }?>
    <?= $form->field($model, 'bookId')->dropDownList($books) ?>


    <?= $form->field($model, 'expectedReturn')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Enter Expected Return date ...'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format'=>'yyyy/mm/dd'
            ]
        ]);
 ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
