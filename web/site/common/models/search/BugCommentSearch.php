<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BugComment;

/**
 * BugCommentSearch represents the model behind the search form about `common\models\BugComment`.
 */
class BugCommentSearch extends BugComment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bug_id', 'created_at', 'created_by'], 'integer'],
            [['comment', 'delete_status'], 'safe'],
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
        $query = BugComment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'bug_id' => $this->bug_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'delete_status', $this->delete_status]);

        return $dataProvider;
    }
}
