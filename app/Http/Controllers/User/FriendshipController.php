<?php

namespace App\Http\Controllers\User;


use App\Helpers\DateTimeHelper;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipController
{
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
            return response()->json(['message' => __('friendship.requestSentSuccessfully')], 201);
        }
        $friendShip = Friendship::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'FOLLOWING',
        ]);
        $friendShip->save();
        return response()->json(['message' => __('friendship.requestSentSuccessfully')], 201);
    }

    public function followBack(Request $request)
    {
        $senderId = $request->user()->id;
        $receiverId = $request->input('receiverId');
        // Check if there is an existing follow request from the receiver to the sender
        $existingFriendship = Friendship::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->where('status', 'FOLLOWING')
            ->first();
        if (!$existingFriendship) {
            return response()->json(['error' => __('friendship.noFollowingRequest')], 404);
        }
        // Update the status to MUTUAL and set the mutual_at timestamp
        $existingFriendship->status = 'MUTUAL';
        $existingFriendship->mutual_at = DateTimeHelper::getCurrentDateTime();
        $existingFriendship->save();
        return response()->json(['message' => __('friendship.followBackSuccessful')], 200);
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
        $friendship->status = 'BLOCKED';
        $friendship->blocker_id = $userId;
        $friendship->save();
        return response()->json(['message' => __('friendship.blockedSuccessfully')], 200);
    }

    public function getFollowers(Request $request)
    {
        $userId = $request->user()->id;
        // Get followers
        $followers = Friendship::with(['sender' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic');
        }])
            ->where('receiver_id', $userId)
            ->where('status', 'FOLLOWING')
            ->get()
            ->pluck('sender')
            ->filter(function ($user) {
                return !is_null($user);
            });

        // Get mutual friends
        $mutualFriends = Friendship::with(['receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic');
        }])
            ->where('sender_id', $userId)
            ->where('status', 'MUTUAL')
            ->get()
            ->pluck('receiver')
            ->filter(function ($user) {
                return !is_null($user);
            });

        // Combine followers and mutual friends
        $allFollowers = $followers->merge($mutualFriends);

        return response()->json(['Followers' => $allFollowers], 200);
    }

    public function getFollowing(Request $request)
    {
        $userId = $request->user()->id;
        // Get followers
        $following = Friendship::with(['receiver' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic');
        }])
            ->where('sender_id', $userId)
            ->where('status', 'FOLLOWING')
            ->get()
            ->pluck('receiver')
            ->filter(function ($user) {
                return !is_null($user);
            });

        // Get mutual friends
        $mutualFriends = Friendship::with(['sender' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic');
        }])
            ->where('receiver_id', $userId)
            ->where('status', 'MUTUAL')
            ->get()
            ->pluck('sender')
            ->filter(function ($user) {
                return !is_null($user);
            });

        // Combine followers and mutual friends
        $allFollowing = $following->merge($mutualFriends);

        return response()->json(['Following' => $allFollowing], 200);
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
                $filteredBlockedUsersId[] = $blockedUser->receiver_id;
            }

        }
        $blockedUsersData = [];
        foreach ($filteredBlockedUsersId as $userId) {
            $user = User::select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic')
                ->find($userId);
            if ($user) {
                $blockedUsersData[] = $user;
            }
        }
        return response()->json(['BlockedUsers' => $blockedUsersData], 200);
//        $blockedUsers = Friendship::where('blocker_id', $userId)
//            ->with(['blocker' => function ($query) {
//                $query->select('id', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'age', 'points', 'rating', 'profile_pic');
//            }])
//            ->get()
//            ->pluck('blocker')
//            ->filter(function ($user) {
//                return !is_null($user);
//            });
//                return response()->json(['BlockedUsers' => $blockedUsers], 200);

    }


}
