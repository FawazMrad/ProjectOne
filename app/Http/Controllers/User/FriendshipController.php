<?php

namespace App\Http\Controllers\User;


use App\Helpers\DateTimeHelper;
use App\Models\Friendship;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class FriendshipController
{
    public function changeNumbers($userId,$number,$type){ //type = following or followers
        $user=User::find($userId);
        $newNumber= ($user->$type)+$number;
        $user->$type=$newNumber;
        $user->save();
    }
    public function sendFollowRequest(Request $request)
    {
        $senderId = $request->user()->id;
        $receiverId = $request->input('receiverId');
        $currentDateTime = DateTimeHelper::getCurrentDateTime();
        $existingFriendship = Friendship::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->where('status', 'FOLLOWING')
            ->first();
        if ($existingFriendship) {
            $existingFriendship->status = 'MUTUAL';
            $existingFriendship->mutual_at = $currentDateTime;
            $existingFriendship->save();
            self::changeNumbers($senderId,1,'following');
            self::changeNumbers($receiverId,1,'followers');
            return response()->json(['message' => __('friendship.requestSentSuccessfully')], 201);
        }
        $friendShip = Friendship::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'FOLLOWING',
        ]);
        $friendShip->save();
        self::changeNumbers($senderId,1,'following');
        self::changeNumbers($receiverId,1,'followers');
        return response()->json(['message' => __('friendship.requestSentSuccessfully')], 201);
    }
    public function cancelFollowing(Request $request)
    {
        $senderId = $request->user()->id;
        $receiverId = $request->input('receiverId');

        $friendship = Friendship::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->first();

        if (!$friendship) {
            return response()->json(['error' => 'Friendship not found.'], 404);
        }

        if ($friendship->status === 'FOLLOWING') {
            Friendship::deleteFriendship($senderId, $receiverId);
            self::changeNumbers($senderId,-1,'following');
            self::changeNumbers($receiverId,-1,'followers');
            return response()->json(['message' => __('friendship.cancelFollowing')], 200);
        } else if ($friendship->status === 'MUTUAL') {
            if ($friendship->sender_id === $senderId && $friendship->receiver_id === $receiverId) {
                $friendship->update([
                    'sender_id' => $receiverId,
                    'receiver_id' => $senderId,
                    'status' => 'FOLLOWING',
                    'mutual_at' => null,
                    'created_at' => DateTimeHelper::getCurrentDateTime(),
                ]);
            } else if ($friendship->sender_id === $receiverId && $friendship->receiver_id === $senderId) {
                $friendship->update([
                    'status' => 'FOLLOWING',
                    'mutual_at' => null,
                ]);
            }
            self::changeNumbers($senderId,-1,'following');
            self::changeNumbers($receiverId,-1,'followers');
            return response()->json(['message' => __('friendship.cancelFollowing')], 200);

        }
    }
    public function blockUser(Request $request)
    {
        $userId = $request->user()->id;
        $targetId = $request->input('targetId');
        $friendship = Friendship::where(function ($query) use ($userId, $targetId) {
            $query->where('sender_id', $userId)
                ->orWhere('sender_id', $targetId);
        })->where(function ($query) use ($userId, $targetId) {
            $query->where('receiver_id', $userId)
                ->orWhere('receiver_id', $targetId);
        })->first();
        if($friendship->status==='BLOCKED') {
            if ($friendship->blocker_id === $userId) {
                Friendship::deleteFriendship($friendship->sender_id, $friendship->receiver_id);
                return response()->json(['message' => __('auth.unBlock')], 200);
            }
            return response()->json(['message'=>'You can not do this action with this user'],400);
        }
            $friendship->status = 'BLOCKED';
            $friendship->blocker_id = $userId;
            $friendship->save();
            self::changeNumbers($userId, -1, 'following');
            self::changeNumbers($targetId, -1, 'followers');
            self::changeNumbers($userId, -1, 'followers');
            self::changeNumbers($targetId, -1, 'following');
            return response()->json(['message' => __('friendship.blockedSuccessfully')], 200);

    }

    public function getFollowers(Request $request)
    {
        $userId = $request->user()->id;
        // Get followers
        $followers = Friendship::with(['sender' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number',  'points', 'rating', 'profile_pic');
        }])
            ->where('receiver_id', $userId)
            ->where('status', 'FOLLOWING')
            ->get()
            ->pluck('sender')
            ->filter(function ($user) {
                return !is_null($user);
            });


// Get mutual friends
        $mutualFriends = Friendship::with(['sender' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'points', 'rating', 'profile_pic');
        }, 'receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'points', 'rating', 'profile_pic');
        }])
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->where('status', 'MUTUAL')
            ->get()
            ->map(function($friendship) use ($userId) {
                // Return the user who is not the current user
                return $friendship->sender_id == $userId ? $friendship->receiver : $friendship->sender;
            })
            ->filter(function($user) {
                return !is_null($user);
            });

        $allFollowers = $followers->merge($mutualFriends);
        return response()->json(['Followers' => $allFollowers,'Followers number'=>$allFollowers->count()], 200);
    }
    public function getFollowing(Request $request){
        $userId=$request->user()->id;
        $following=self::getFollowingStatic($userId);
        return response()->json(['Following' => $following['Following'],'Following number'=>$following['FollowingNumber']],$following['statusCode'] );

    }
    public static function getFollowingStatic($userId)
    {
        $following = Friendship::with(['receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number',  'points', 'rating', 'profile_pic');
        }])
            ->where('sender_id', $userId)
            ->where('status', 'FOLLOWING')
            ->get()
            ->pluck('receiver')
            ->filter(function ($user) {
                return !is_null($user);
            });

        $mutualFriends = Friendship::with(['sender' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'points', 'rating', 'profile_pic');
        }, 'receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'points', 'rating', 'profile_pic');
        }])
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->orWhere('sender_id', $userId);
            })
            ->where('status', 'MUTUAL')
            ->get()
            ->map(function ($friendship) use ($userId) {
                if ($friendship->sender_id == $userId) {
                    return $friendship->receiver;
                } else {
                    return $friendship->sender;
                }
            })
            ->filter(function ($user) {
                return !is_null($user);
            });

        // Combine followers and mutual friends
        $allFollowing = $following->merge($mutualFriends);

        return ['Following' => $allFollowing,'FollowingNumber'=>$allFollowing->count(),'statusCode'=> 200];
    }
    public function getBlocked(Request $request)
    {
        $userId = $request->user()->id;
        $blockedUsers = Friendship::where('blocker_id', $userId)->get();
        $filteredBlockedUsersId = [];

        foreach ($blockedUsers as $blockedUser) {
            if ($blockedUser['sender_id'] === $userId) {
                $filteredBlockedUsersId[] = $blockedUser->receiver_id;
            } else if ($blockedUser['receiver_id'] === $userId) {
                $filteredBlockedUsersId[] = $blockedUser->sender_id;
            }

        }
        $blockedUsersData = [];
        foreach ($filteredBlockedUsersId as $userId) {
            $user = User::select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'points', 'rating', 'profile_pic')
                ->find($userId);
            if ($user) {
                $blockedUsersData[] = $user;
            }
        }
        return response()->json(['BlockedUsers' => $blockedUsersData,'BlockedUsersNumber'=>count($blockedUsersData)], 200);


    }


}
