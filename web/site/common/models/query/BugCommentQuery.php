<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\BugComment]].
 *
 * @see \common\models\BugComment
 */
class BugCommentQuery extends \common\components\MyCustomActiveRecordQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\BugComment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\BugComment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
