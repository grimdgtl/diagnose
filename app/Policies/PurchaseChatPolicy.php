<?php

namespace App\Policies;

use App\Models\PurchaseChat;
use App\Models\User;

class PurchaseChatPolicy
{
    /* --------------------------------------------------------------
     |  PREGLED
     |-------------------------------------------------------------- */
    public function viewAny(User $user): bool
    {
        return false;            // nema globalne liste
    }

    public function view(User $user, PurchaseChat $chat): bool
    {
        return $chat->user_id === $user->id;
    }

    /* --------------------------------------------------------------
     |  Ostale akcije trenutno nisu dozvoljene
     |-------------------------------------------------------------- */
    public function create(User $user): bool      { return false; }
    public function update(User $user, PurchaseChat $chat): bool { return false; }
    public function delete(User $user, PurchaseChat $chat): bool { return false; }
    public function restore(User $user, PurchaseChat $chat): bool { return false; }
    public function forceDelete(User $user, PurchaseChat $chat): bool { return false; }
}
