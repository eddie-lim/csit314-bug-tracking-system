<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SystemAuditTrail;

/**
 * SystemAuditTrailSearch represents the model behind the search form about `common\models\SystemAuditTrail`.
 */
class SystemAuditTrailSearch extends SystemAuditTrail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'row_id', 'created_by', 'created_at'], 'integer'],
            [['model', 'controller', 'action', 'value'], 'safe'],
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
        $query = SystemAuditTrail::find()->orderBy(['created_at'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'row_id' => $this->row_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
