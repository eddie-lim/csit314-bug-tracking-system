<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\BugTag]].
 *
 * @see \common\models\BugTag
 */
class BugTagQuery extends \common\components\MyCustomActiveRecordQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\BugTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\BugTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
