<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\chat_salon;
use App\salon_customer;
use App\role;
use App\customer;
use App\service;
use App\booking;
use App\booking_item;
use Hash;
use session;
use Auth;
use Carbon\Carbon;
use DB;
use App\Events\ChatEvent;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getBooking(){
      $booking = booking::where('salon_id',Auth::user()->user_id)->get();
      $customer = customer::all();
      return view('vendor.booking',compact('booking','customer'));
  }
    
    public function chatToCustomer($id){
        $booking = booking::find($id);
        $customer = customer::find($booking->customer_id);
        $item =DB::table('booking_items as b')
        ->where('b.booking_id',$booking->id)
        ->join('services as s', 's.id', '=', 'b.service_id')
        ->select('s.id','s.service_name_english','b.price')
        ->get();
        return view('vendor.chat_to_customer',compact('customer','item','booking'));
    }

    public function saveCustomerChat(Request $request){
      $request->validate([
          'salon_text'=>'required',
      ]);
      date_default_timezone_set("Asia/Dubai");
      date_default_timezone_get();
      $salon_customer = new salon_customer;
      $salon_customer->text = $request->salon_text;
      $salon_customer->customer_id = $request->customer_id;
      $salon_customer->booking_id = $request->booking_id;
      $salon_customer->salon_id = Auth::user()->user_id;
      $salon_customer->message_from = 1;
      $salon_customer->save();

      $dateTime = new Carbon($salon_customer->updated_at, new \DateTimeZone('Asia/Dubai'));
        $message =  array(
           'message'=> $salon_customer->text,
           'message_from'=> '0',
           'date'=> $dateTime->diffForHumans(),
           'channel_name'=> $salon_customer->booking_id,
        );
        event(new ChatEvent($message));
      return response()->json($request->booking_id); 
    }

    public function getCustomerChat($id){
        $chat = salon_customer::where('booking_id',$id)->get();

        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
        $output=''; 
    foreach($chat as $row){
        $dateTime = new Carbon($row->updated_at, new \DateTimeZone('Asia/Dubai'));
        if($row->message_from == 0){
        $output.='<div class="chat chat-left">
          <div class="chat-body">
            <div class="chat-message">
              <p>'.$row->text.'</p>
              <span style="left:10px !important;" class="chat-time">'.$dateTime->diffForHumans().'</span>
            </div>
          </div>
        </div>';
        }
        else{
        $output.='<div class="chat">
          <div class="chat-body">
            <div class="chat-message">
              <p>'.$row->text.'</p>
              <span class="chat-time">'.$dateTime->diffForHumans().'</span>
            </div>
          </div>
        </div>';
        }
    }

$output.='<script src="/app-assets/js/scripts/pages/app-chat.js"></script>
<script>
chatContainer.scrollTop($(".chat-container > .chat-content").height());
</script>
';
         
        return response()->json(['html'=>$output],200); 
    }
}
