<?php

namespace App\Http\Controllers\Api\Chat;

use App\Helper\ResponseBuilder;
use App\Http\Resources\Chat\SingleChatMessageCollection;
use App\Http\Resources\Chat\GroupChatMessageCollection;
use App\Http\Resources\Chat\GroupListCollection;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\RestrictedWord;
use App\Models\ChatMessage;
use App\Models\MessageSeen;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupChat;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\UsersExist;
use App\Rules\NotInGroup;
use App\Rules\UniqueRestrictedWord;
use App\Rules\UserExistsInGroup;
use App\Rules\UserInGroup;
use Validator;
use DB;
use Carbon\Carbon;

class ChatController extends Controller
{

    public function createGroup(Request $request) {
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $logedUser = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|unique:groups,name,'.$request->group_id,
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:1024',
            'discription' => 'nullable|string',
            'members' => ['required', 'string', new UsersExist],
        ],[
            'members.required' => 'Atleast one member is required'
        ]);
       if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        try {
            $data = [
                'creator_id' => $logedUser->id,
                'name' => $request->group_name,
                'is_admin_post' => $request->admin_post_only ?? 0,
                'desc' => $request->discription?? null,
                'is_group' => 1,
            ];
            if($request->hasfile('image')){
                $data['image']  = Helper::storeImage($request->file('image'),config('app.group_image'));
            }
            $groupData = Group::updateOrCreate(['id' => $request->group_id],$data);
    
            $members[] = [
                'group_id'  =>  $groupData->id,
                'user_id'   =>  $logedUser->id
            ];
            foreach(explode(',',$request->members) as $value){
                $members[] = [
                    'group_id'  =>  $groupData->id,
                    'user_id'   =>  (int)$value
                ];
            }
            GroupMember::insert($members);
            $msg = isset($request->group_id) ? 'Group updated successfully':'Group created successfully';
            return ResponseBuilder::successMessage($msg, $this->success);
        } catch (\Throwable $th) {
            throw $th;
            return ResponseBuilder::error("Something went wrong", $this->serverError);
        }
    }

    public function addMemberGroup(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $logedUser = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'members' => ['required', 'string', new UsersExist, new NotInGroup($request->group_id)],
        ],[
            'members.required' => 'Atleast one member is required'
        ]);
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        $members = [];
        foreach(explode(',',$request->members) as $value){
            $members[] = [
                'group_id'  =>  (int)$request->group_id,
                'user_id'   =>  (int)$value
            ];
        }
        GroupMember::insert($members);
        return ResponseBuilder::successMessage("Added successfully", $this->success);
    }

    public function addRestrictedWord(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'word' => ['required', new UniqueRestrictedWord($request->group_id)],
        ], [
            'word.required' => 'The restricted word field is required.'
        ]);
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        RestrictedWord::create([
            'group_id'  => $request->group_id,
            'word'  => $request->word
        ]);
        return ResponseBuilder::successMessage("word added successfully", $this->success);
    }

    public function deleteRestrictedWord($id){
        $word = RestrictedWord::find($id);
        if(isset($word)){
            $word->delete();
            return ResponseBuilder::successMessage("Word deleted successfully", $this->success);
        }else{
            return ResponseBuilder::successMessage("Word not found", $this->notFound);
        }
    }

    public function restrictedWordList(Request $request){

        $data = RestrictedWord::where('group_id',$request->gid)->get()
                ->map(function($value){
                    return [
                        'id'    => $value->id,
                        'word'    => $value->word,
                    ];
                });

        return ResponseBuilder::successMessage("Word list", $this->success,$data);
    }

    public function sendMessage(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $validator = Validator::make($request->all(), [
            'group_id' => 'nullable|exists:groups,id',
            'receiver_id' => 'required|exists:users,id',
            'message_id' => 'nullable|exists:chats,id',
            'message' => 'nullable|string|min:1|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,pdf,mp4,mov,avi|max:10240', 
        ]);

        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        $user = auth()->user();
        try {
            if(!isset($request->group_id)){
                $gData = Group::create([
                    'creator_id'  => $user->id
                ]);
                // creating members
                $members =[[
                    'group_id'  =>  $gData->id,
                    'user_id'   =>  $user->id
                ],[
                    'group_id'  =>  $gData->id,
                    'user_id'   =>  $request->receiver_id
                ]];

                GroupMember::insert($members);
            }
            $data = [
                'group_id'  => isset($request->group_id)?$request->group_id:$gData->id,
                'reply_id'  => $request->message_id??null,
                'message'  => is_null($request->message)?null:Helper::encryptMessage($request->message),
                'sender_id'  => $user->id,
            ];
            if($request->hasfile('file')){
                $data['file']  = Helper::storeImage($request->file('file'),config('app.chat_file'));
            }
            GroupChat::create($data);
            return ResponseBuilder::successMessage("Message sent successfully", $this->success);
        } catch (\Throwable $th) {
            return ResponseBuilder::successMessage("Opps! Something went wrong", $this->serverError);
            //throw $th;
        }
    }

    public function singleChatList($reciever_id){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $allChat = Chat::where('sender_id',auth()->user()->id)
                    ->where('receiver_id',$reciever_id)->latest()->get();
        $data = new SingleChatMessageCollection($allChat);
        return ResponseBuilder::successMessage("message list", $this->success,$data);
    }

    public function seenMessage(Request $request) {
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        Chat::where(['checkout'=>0,'receiver_id'=>auth()->user()->id,'sender_id'=>$request->sender_id])->update(['checkout'=>1]);
        return ResponseBuilder::successMessage("Message seen successfully", $this->success);
    }

    public function sendMessageGroup(Request $request) {
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'message_id' => 'nullable|exists:group_chats,id',
            'message' => 'nullable|string|min:1|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,pdf,mp4,mov,avi|max:10240', 
            'message' => 'required_if:file,null',
            'file' => 'required_if:message,null',
        ],[
            'message.required_if'=>'You can type message or select file to send message'
        ]);
        
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        $data = [
            'group_id'  => $request->group_id,
            'reply_id'  => $request->message_id??null,
            'message'  => is_null($request->message)?null:Helper::encryptMessage($request->message),
            'sender_id'  => auth()->user()->id,
        ];
        if($request->hasfile('file')){
            $data['file']  = Helper::storeImage($request->file('file'),config('app.chat_file'));
        }
        GroupChat::create($data);
        return ResponseBuilder::successMessage("Message sent successfully", $this->success);
    }

    public function groupSeenMessage(Request $request)
    {
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);
    
        $user = auth()->user();
    
        $validator = Validator::make($request->all(), [
            'group_id' => ['required', 'exists:groups,id', new UserExistsInGroup($user->id)],
        ]);
    
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        $groupChatIds = GroupChat::where('group_id', $request->group_id)
            ->where('sender_id', '!=', $user->id)
            ->pluck('group_chats.id'); 
        $unseenMessageIds = $groupChatIds->diff($user->seenMessages()->pluck('message_id'));
        $seenData = [];
        foreach($unseenMessageIds as $val){
            $seenData[] = [
                'message_id'    =>$val,
                'group_id'    =>$request->group_id,
                'user_id'    =>$user->id,
            ];
        }
        MessageSeen::insert($seenData);
        return ResponseBuilder::successMessage("Message seen successfully", $this->success);
    }
    
    public function groupChatList($group_id){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $allChat = GroupChat::where('group_id',$group_id)->latest()->get();
        $data = new GroupChatMessageCollection($allChat);
        return ResponseBuilder::successMessage("message list", $this->success,$data);
    }

    public function editMessage(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:group,single',
            'message' => 'required|string|min:1|max:255',
            'message_id'    => 'required'
        ]);
        
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        
        if($request->type == 'group'){
            $msg = GroupChat::where(['id'=>$request->message_id,'sender_id'=>auth()->user()->id])->whereNull('deleted_at')->first();
        }else{
            $msg = Chat::where(['id'=>$request->message_id,'sender_id'=>auth()->user()->id])->whereNull('deleted_at')->first();
        }
        if(!$msg) return ResponseBuilder::error("Message not found", $this->notFound);

        $msg->message = Helper::encryptMessage($request->message);
        $msg->is_edited = 1;
        $msg->save();
        return ResponseBuilder::successMessage("Message edited successfully", $this->success);
    }

    public function deleteMessage(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:group,single',
            'message_id'    => 'required'
        ]);
        
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        if($request->type == 'group'){
            $msg = GroupChat::where(['id'=>$request->message_id,'sender_id'=>auth()->user()->id])->whereNull('deleted_at')->first();
        }else{
            $msg = Chat::where(['id'=>$request->message_id,'sender_id'=>auth()->user()->id])->whereNull('deleted_at')->first();
        }
        if(!$msg) return ResponseBuilder::error("Message not found", $this->notFound);

        $msg->deleted_at = Carbon::now();
        $msg->save();
        return ResponseBuilder::successMessage("Message deleted successfully", $this->success);
    }

    public function allList(){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $user = auth()->user();

        $gids = GroupMember::where(['user_id'=>$user->id])->pluck('group_id')->toArray();
        $groups = Group::whereIn('id',$gids)->get();
        $this->response = new GroupListCollection($groups);
        return ResponseBuilder::successMessage("User and group list", $this->success,$this->response);
    }

    public function blockUser(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);

        $user = auth()->user();
    }

    public function makeAdmin(Request $request){
        if((!Auth::guard('api')->check())) return ResponseBuilder::error("User not found", $this->unauthorized);
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group_id' => ['required', 'exists:groups,id', new UserExistsInGroup($user->id)],
            'admin_id'    => ['required',new UserInGroup($request->group_id)]
        ]);
        
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        $group = Group::find($request->group_id);
        if(!$group) return ResponseBuilder::error("Group not found", $this->notFound);
        if($group->creator_id != $user->id) return ResponseBuilder::error("You are not admin", $this->unauthorized);
        $group->creator_id = $request->admin_id;
        $group->save();
        return ResponseBuilder::successMessage("Admin change successfully", $this->success);
    }

    public function removeFromGroup(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group_id' => ['required', 'exists:groups,id', new UserExistsInGroup($user->id)],
            'user_id'    => ['required',new UserInGroup($request->group_id)]
        ]);
        
        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
        try {
            $group = Group::find($request->group_id);
            if($group->creator_id != $user->id) return ResponseBuilder::error("You are not admin", $this->unauthorized);
            GroupMember::where(['group_id'=>$request->group_id,'user_id'=>$request->user_id])->delete();
            return ResponseBuilder::successMessage("User remove successfully", $this->success);
        } catch (\Throwable $th) {
            return ResponseBuilder::successMessage("Opps! Something went wrong", $this->serverError);
            //throw $th;
        }
    }

}