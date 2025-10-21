<?php

if (!function_exists('getRoleName')) {
    function getRoleName($roleId)
    {
        $roles = [
            1 => 'Commex',
            9 => 'Dean',
            10 => 'ASD',
            11 => 'AD',
        ];

        return $roles[$roleId] ?? 'Approver';
    }
}