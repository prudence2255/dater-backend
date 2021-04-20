<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Traits\MediaUpload;
use Illuminate\Support\Facades\DB;
use App\Events\NewMessage;
use App\Http\Resources\ThreadsResource;
use App\Http\Resources\ThreadResource;

class MessagesController extends Controller
{

    use MediaUpload;

    public $message;
    public $thread;
    public function __construct(){
        $this->middleware('auth:client');
    }
    /**
     * display all messages associated with the current user
     */
    public function getThreads(){
        $user = request()->user();
    /**
     * messages sent by user
     */
   // $messages = $user->messages()->get();
   /**
    * new messages count for user
    */
    //$messages = $user->unreadMessagesCount();
    /**
     * returns new messages count for user
     */
    //$messages = $user->threadsWithNewMessages();

    /**
     * threads the user is associated with
     */
    //$messages = Thread::forUser($user->id);

    /**
     * Returns threads with new messages that the user is associated with.
     */
    //$messages = Thread::ForUserWithNewMessages($user->id);

    /**
     * Returns array of unread messages in thread for given user
     */
    //messages = Thread::userUnreadMessages($user->id);

    /**
     * Returns count of unread messages in thread for given user
     */
    //$messages = Thread::userUnreadMessagesCount($user->id);

    /**
     * returns all threads with their associated messages for which the current user
     * participated
     */
    $messages = $user->threadsWithMessagesWithUsers($user->id)->get();

    /**
     * Returns unread messages given the userId.
     */
    //$messages = Message::unreadForUser($user->id)->get();

    return ThreadsResource::collection($messages);
    //return response()->json($messages);
}

    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {

        // All threads, ignore deleted/archived participants
        $threads = Thread::getAllLatest()->get();


    return response()->json($threads);
    }

    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json('error');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $userId = request()->user()->id;
        $users = Client::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        $thread->markAsRead($userId);
        //return response()->json(['thread' => $thread, 'clients' => $users]);
        return new ThreadResource($thread);

    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */

    public function store()
    {
        DB::transaction(function ()  {

            $url;

            if(request()->hasFile('file')){
              $url = $this->files();
            }



         $this->thread = Thread::create([
                'subject' => bin2hex(random_bytes(10)),
            ]);

            // Message
           $this->message = Message::create([
                'thread_id' => $this->thread->id,
                'user_id' => request()->user()->id,
                'body' => request()->body ?? null,
                'file_url' => $url ?? null,
                'type' => request()->type ?? null
            ]);

            // Sender
            Participant::create([
                'thread_id' => $this->thread->id,
                'user_id' => request()->user()->id,
                'last_read' => new Carbon,
            ]);

            // Recipients
            if (request()->has('recipients')) {
                $this->thread->addParticipant((int) request()->recipients);
            }
        });

        /**
         * dispatches an event
         */
        event(new NewMessage(
            [
                'id' => $this->thread->id,
                'count' => $this->thread->userUnreadMessagesCount(request()->user()->id),
                'users' => $this->thread->users,
                'participants' => $this->thread->participants,
                'profile_pictures' => collect($this->thread->users)->map(function($user){
                    return [
                        'profile_picture' => $user->profilePictures()->latest('created_at')->first()
                    ];
                }),
                'messages' => $this->thread->messages
            ]
        ));


        return new ThreadResource($this->thread);
    }

    /**
     * upload message file
     */
    public function uploadMessageFile(){
        $file = $this->files();
        return response()->json($file);
    }

    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        DB::transaction(function () use($id){
            $url;
            if(request()->hasFile('file')){
              $url = $this->files();
            }

            try {
                $this->thread = Thread::findOrFail($id);
            } catch (ModelNotFoundException $e) {

            //
            }

            $this->thread->activateAllParticipants();

            // Message
           $this->message = Message::create([
                'thread_id' => $this->thread->id,
                'user_id' => request()->user()->id,
                'body' => request()->body ?? null,
                'file_url' => $url ?? null,
                'type' => request()->type
            ]);

            // Add replier as a participant
            $participant = Participant::firstOrCreate([
                'thread_id' => $this->thread->id,
                'user_id' => request()->user()->id,
            ]);

            $participant->last_read = new Carbon;
            $participant->save();

            // Recipients
            if (request()->has('recipients')) {
                $this->thread->addParticipant((int) request()->recipients);
            }
        });

         /**
         * dispatches an event
         */
        event(new NewMessage(
            [
                'id' => $this->thread->id,
                'count' => $this->thread->userUnreadMessagesCount(request()->user()->id),
                'users' => $this->thread->users,
                'participants' => $this->thread->participants,
                'profile_pictures' => collect($this->thread->users)->map(function($user){
                    return [
                        'profile_picture' => $user->profilePictures()->latest('created_at')->first()
                    ];
                }),
                'messages' => $this->thread->messages
            ]
        ));

     return new ThreadResource($this->thread);


    }
}
