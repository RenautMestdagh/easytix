<?php

namespace App\Traits;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait DeleteUser
{
    //
    public function deleteUser($id)
    {
        $this->authorize('users.delete');

        if (auth()->id() === (int) $id) {
            $this->flashMessage('You cannot delete your own account.', 'error');
            return;
        }

        try {
            DB::starttransaction();

            $user = User::with(['roles' => function($query) {
                $query->lockForUpdate();
            }])->lockForUpdate()->findOrFail($id);

            if ($user->organization_id && $user->roles->first()->name === 'admin') {
                // Lock all organization admins
                $adminCount = Organization::findOrFail($user->organization_id)
                    ->admins()
                    ->lockForUpdate()
                    ->count();

                if ($adminCount <= 1) {
                    DB::rollBack();
                    $this->flashMessage('Cannot delete the last admin in the organization.', 'error');
                    return;
                }
            }
            $user->delete();
            DB::commit();

            if(property_exists($this, 'adminCount') && $user->roles->first()->name === 'admin')
                $this->adminCount = $this->organization->admins()->count();
            
            $this->flashMessage('User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            $this->flashMessage('Error while deleting user.', 'error');
        }
    }

    public function forceDeleteUser($id)
    {
        $this->authorize('users.delete');
        try {
            User::withTrashed()->findOrFail($id)->forceDelete();
            $this->flashMessage('User permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting user: ' . $e->getMessage());
            $this->flashMessage('Error while permanently deleting user.', 'error');
        }
    }

    public function restoreUser($id)
    {
        $this->authorize('users.delete');
        try{
            User::withTrashed()->findOrFail($id)->restore();
            if (property_exists($this, 'adminCount'))
                $this->adminCount = $this->organization->admins()->count();
            $this->flashMessage('User restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage());
            $this->flashMessage('Error restoring user.', 'error');
        }
    }
}
