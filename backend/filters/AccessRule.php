<?php

namespace backend\filters;

use common\models\User;

class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @param \yii\web\User $user
     *
     * @return bool
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        $isGuest = $user->getIsGuest();

        $userRole = $user->identity ? $user->identity->roleObject->name : null;

        foreach ($this->roles as $role) {
            switch ($role) {
                case '?':
                    return $isGuest;
                break;
                case '@':
                    return !$isGuest;
                break;
                default:
                    if (!$isGuest && $role == $userRole) {
                        return true;
                    }
                    //return $user->can($role);
            }
        }

        return false;
    }
}