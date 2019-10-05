<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\SocialAccount;
use App\User;

class SocialAccountService
{
    public static function createOrGetUser(ProviderUser $providerUser, $social)
    {
        
        $account = SocialAccount::whereProvider($social)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        // SocialAccount::whereProvider($social)
        //     ->whereProviderUserId($providerUser->getId())->delete();
        //exit;
        
        if ($account) {
            return $account->user;
        } else {
            $email = $providerUser->getEmail();
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $social
            ]);
            
            $user = User::whereEmail($email)->first();
            if (!$user) {

                $user = User::create([
                    'email' => $email,
                    'name' => $providerUser->getName(),
                    'password' => null,
                ]);
            }

            $account->user()->associate($user);
            $account->save();
            
            return $user;
        }
    }
}