<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BugAction;

/**
 * BugActionSearch represents the model behind the search form about `common\models\BugAction`.
 */
class BugActionSearch extends BugAction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bug_id', 'created_at', 'created_by'], 'integer'],
            [['action_type', 'notes', 'delete_status'], 'safe'],
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
        $query = BugAction::find();

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

        $query->andFilterWhere(['like', 'action_type', $this->action_type])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'delete_status', $this->delete_status]);

        return $dataProvider;
    }
}
