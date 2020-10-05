<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\BugDocument]].
 *
 * @see \common\models\BugDocument
 */
class BugDocumentQuery extends \common\components\MyCustomActiveRecordQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\BugDocument[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\BugDocument|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
