<?php

namespace common\models;

use Yii;

/**
 * ZModel override the default ActiveRecord \yii\db\ActiveRecord.
 *
 * @inheritdoc
 *
 * @property string $created
 * @property string $updated
 * @property string $createdDate
 * @property string $updatedDate
 * @property string $createdTime
 * @property string $updatedTime
 * @property boolean $isRead
 * @property boolean $isEdit
 */
class ZModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = array_merge(
            parent::fields(),
            $this->extraFields(),
            [
                'isRead' => 'isRead', 
                'isEdit' => 'isEdit',
                'created' => 'created',
                'updated' => 'updated',
                'createdDate' => 'createdDate',
                'updatedDate' => 'updatedDate',
                'createdTime' => 'createdTime',
                'updatedTime' => 'updatedTime',
            ]
        );

        return $fields;
    }

    /**
     * 2017-05-06 12:25:15
     * @return mixed|string
     */
    public function getCreated()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        return Yii::$app->formatter->asDatetime($this->created_at);
    }

    /**
     * 2017-05-06 12:25:15
     * @return mixed|string
     */
    public function getUpdated()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        return Yii::$app->formatter->asDatetime($this->updated_at);
    }

    /**
     * 2017-05-06
     * @return mixed|string
     */
    public function getCreatedDate()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        return Yii::$app->formatter->asDate($this->created_at);
    }

    /**
     * 2017-05-06
     * @return mixed|string
     */
    public function getUpdatedDate()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        return Yii::$app->formatter->asDate($this->updated_at);
    }

    /**
     * 12:25:15
     * @return mixed|string
     */
    public function getCreatedTime()
    {
        if (!$this->hasAttribute('created_at') || !$this->created_at) {
            return null;
        }
        return Yii::$app->formatter->asTime($this->created_at);
    }

    /**
     * 12:25:15
     * @return mixed|string
     */
    public function getUpdatedTime()
    {
        if (!$this->hasAttribute('updated_at') || !$this->updated_at) {
            return null;
        }
        return Yii::$app->formatter->asTime($this->updated_at);
    }

    /**
     * if current user can read this object
     * @return bool
     */
    public function getIsRead()
    {
        return true;
    }

    /**
     * if current user can edit/add/delete this object
     * @return bool
     */
    public function getIsEdit()
    {
        return true;
    }

    /**
     * @param integer $status
     * @return bool
     */
    public function changeStatus($status)
    {
        if ($this->hasAttribute('status')) {
            $this->status = (int)$status;
            return $this->save();
        }
        return false;
    }

}
