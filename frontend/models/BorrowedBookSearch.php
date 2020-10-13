<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\BorrowedBook;

/**
 * BorrowedBookSearch represents the model behind the search form of `frontend\models\BorrowedBook`.
 */
class BorrowedBookSearch extends BorrowedBook
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bbId', 'studentId', 'bookId'], 'integer'],
            [['borrowDate', 'expectedReturn', 'actualReturnDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
       
        if(\Yii::$app->user->can('librarian')){
            $query = BorrowedBook::find()->where(['actualReturnDate'=>NULL]);
        }
        
        if(\Yii::$app->user->can('admin')){
            $query = BorrowedBook::find()->where(['actualReturnDate'=>NULL]);
        }
        if(\Yii::$app->user->can('student')){
            $studentId = Student::find()->where(['userId'=>\yii::$app->user->id])->one();
            $query = BorrowedBook::find()->where(['actualReturnDate'=>NULL])->andWhere(['studentId'=>$studentId->studentsId]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bbId' => $this->bbId,
            'studentId' => $this->studentId,
            'bookId' => $this->bookId,
            'borrowDate' => $this->borrowDate,
            'expectedReturn' => $this->expectedReturn,
            'actualReturnDate' => $this->actualReturnDate,
        ]);

        return $dataProvider;
    }
}
