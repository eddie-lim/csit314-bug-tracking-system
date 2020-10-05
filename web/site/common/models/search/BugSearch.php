<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Bug;

/**
 * BugSearch represents the model behind the search form about `common\models\Bug`.
 */
class BugSearch extends Bug
{
    /*Class Variables*/
    public $filterBy = ["new", "assigned", "fixing", "pending_review", "completed", "rejected", "reopen"];
    public $assignedTo;

    public function setFilterBy($byWhat) {
      $this->filterBy = $byWhat;
    }

    public function setAssignedTo($toWho) {
      $this->developer_user_id = $toWho;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'developer_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'description', 'bug_status', 'priority_level', 'notes', 'delete_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Bug::find();
        $query->andWhere(["bug_status" => $this->filterBy]);
        $query->andWhere(["developer_user_id" => $this->assignedTo]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'developer_user_id' => $this->developer_user_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'bug_status', $this->bug_status])
            ->andFilterWhere(['like', 'priority_level', $this->priority_level])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'delete_status', $this->delete_status]);

        return $dataProvider;
    }
}
